<?php

namespace App\Audit;

use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Turns database changes into audit_logs rows. Auditing must never break the real work, so
 * everything here is wrapped: if writing a log line fails, the failure is logged and the app carries on.
 */
class AuditWriter
{
    private static bool $busy = false;

    public static function skips(string $table): bool
    {
        if (! config('audit.enabled') || self::$busy) {
            return true;
        }
        if (in_array($table, config('audit.skip_tables', []), true)) {
            return true;
        }
        foreach (config('audit.skip_prefixes', []) as $prefix) {
            if (str_starts_with($table, $prefix)) {
                return true;
            }
        }

        return false;
    }

    public static function maxRows(): int
    {
        return (int) config('audit.max_rows', 200);
    }

    // ---- statements ------------------------------------------------------

    public static function inserted(string $table, array $values, $insertId = null, string $action = 'created'): void
    {
        self::guard(function () use ($table, $values, $insertId, $action) {
            $rows = (isset($values[0]) && is_array($values[0])) ? $values : [$values];
            $entries = [];
            foreach (array_slice($rows, 0, self::maxRows()) as $row) {
                $new = self::clean($row);
                $entries[] = [
                    'action' => $action,
                    'table_name' => $table,
                    'record_id' => count($rows) === 1 ? ($insertId ?? ($new['id'] ?? null)) : ($new['id'] ?? null),
                    'old_values' => null,
                    'new_values' => $new,
                ];
            }
            self::write($entries, count($rows) > self::maxRows() ? 'Only the first '.self::maxRows().' of '.count($rows).' rows were logged.' : null);
        });
    }

    /**
     * @param  array<int, array>  $before  the rows as they were
     * @param  array<int|string, array>  $afterById  the same rows afterwards, keyed by id (empty if the table has no id)
     */
    public static function updated(string $table, array $before, array $afterById, array $values, int $affected): void
    {
        self::guard(function () use ($table, $before, $afterById, $values, $affected) {
            $ignored = config('audit.ignored_columns', []);
            $entries = [];

            foreach ($before as $old) {
                $id = $old['id'] ?? null;
                $new = ($id !== null && isset($afterById[$id])) ? $afterById[$id] : null;

                if ($new === null) {
                    // No id to re-read the row by: compare against the values that were set
                    $new = $old;
                    foreach ($values as $column => $value) {
                        $new[$column] = $value instanceof Expression ? '(expression)' : $value;
                    }
                }

                $oldChanged = [];
                $newChanged = [];
                foreach ($new as $column => $value) {
                    $was = $old[$column] ?? null;
                    if (self::scalar($was) !== self::scalar($value)) {
                        $oldChanged[$column] = $was;
                        $newChanged[$column] = $value;
                    }
                }
                $meaningful = array_diff(array_keys($newChanged), $ignored);
                if (empty($meaningful)) {
                    continue;
                }

                $action = 'updated';
                if ($meaningful === ['deleted_at']) {
                    $action = ($oldChanged['deleted_at'] ?? null) === null ? 'deleted' : 'restored';
                }

                $entries[] = [
                    'action' => $action,
                    'table_name' => $table,
                    'record_id' => $id,
                    'old_values' => self::clean($oldChanged),
                    'new_values' => self::clean($newChanged),
                ];
            }

            self::write($entries, $affected > count($before) ? 'Only the first '.count($before)." of {$affected} rows were logged." : null);
        });
    }

    public static function deleted(string $table, array $rows, int $affected): void
    {
        self::guard(function () use ($table, $rows, $affected) {
            $entries = [];
            foreach ($rows as $row) {
                $entries[] = [
                    'action' => 'deleted',
                    'table_name' => $table,
                    'record_id' => $row['id'] ?? null,
                    'old_values' => self::clean($row),
                    'new_values' => null,
                ];
            }
            self::write($entries, $affected > count($rows) ? 'Only the first '.count($rows)." of {$affected} rows were logged." : null);
        });
    }

    public static function truncated(string $table): void
    {
        self::guard(fn () => self::write([['action' => 'truncated', 'table_name' => $table, 'record_id' => null, 'old_values' => null, 'new_values' => null]], 'Every row in the table was removed.'));
    }

    /** A non-database event: sign-in, sign-out, failed sign-in. */
    public static function event(string $action, ?string $notes = null, $user = null): void
    {
        self::guard(function () use ($action, $notes, $user) {
            if (! config('audit.enabled')) {
                return;
            }
            self::write([['action' => $action, 'table_name' => null, 'record_id' => null, 'old_values' => null, 'new_values' => null]], $notes, $user);
        });
    }

    // ---- internals -------------------------------------------------------

    private static function write(array $entries, ?string $notes = null, $user = null): void
    {
        if (empty($entries)) {
            return;
        }

        $actor = AuditContext::actor($user);
        $request = AuditContext::request();
        $operation = AuditContext::operation();
        $batch = (string) Str::uuid();
        $now = now();

        $records = [];
        foreach ($entries as $i => $entry) {
            $records[] = [
                'batch_id' => $batch,
                'user_id' => $actor['user_id'],
                'username' => $actor['username'] ? mb_substr($actor['username'], 0, 120) : null,
                'user_name' => $actor['user_name'] ? mb_substr($actor['user_name'], 0, 190) : null,
                'action' => $entry['action'],
                'table_name' => $entry['table_name'],
                'record_id' => $entry['record_id'] !== null ? mb_substr((string) $entry['record_id'], 0, 64) : null,
                'old_values' => $entry['old_values'] !== null ? json_encode($entry['old_values'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) : null,
                'new_values' => $entry['new_values'] !== null ? json_encode($entry['new_values'], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) : null,
                'operation' => $operation,
                'ip' => $request['ip'],
                'http_method' => $request['http_method'],
                'url' => $request['url'],
                'user_agent' => $request['user_agent'],
                'notes' => $i === 0 ? $notes : null,
                'created_at' => $now,
            ];
        }

        self::$busy = true;
        try {
            DB::table('audit_logs')->insert($records);
        } finally {
            self::$busy = false;
        }
    }

    private static function guard(callable $work): void
    {
        try {
            $work();
        } catch (\Throwable $e) {
            self::$busy = false;
            Log::error('Audit log write failed: '.$e->getMessage());
        }
    }

    /** Make a row safe to store: hide secrets, flatten odd values, cap huge ones. */
    private static function clean(array $row): array
    {
        $masked = array_map('strtolower', config('audit.masked_columns', []));
        $out = [];
        foreach ($row as $column => $value) {
            if (in_array(strtolower((string) $column), $masked, true)) {
                $out[$column] = '[hidden]';

                continue;
            }
            if ($value instanceof Expression) {
                $value = '(expression)';
            } elseif ($value instanceof \DateTimeInterface) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (is_resource($value)) {
                $value = '[binary]';
            } elseif (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
            } elseif (is_string($value) && mb_strlen($value) > 5000) {
                $value = mb_substr($value, 0, 5000).'... [cut]';
            }
            $out[$column] = $value;
        }

        return $out;
    }

    private static function scalar($value): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof Expression) {
            return '(expression)';
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }
}
