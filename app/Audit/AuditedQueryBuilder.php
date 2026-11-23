<?php

namespace App\Audit;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;

/**
 * A query builder that records every insert, update and delete in the audit log.
 *
 * Eloquent models, repositories and raw DB::table() calls all end up here, so nothing slips past
 * the way it would if we only listened to model events (those do not fire for ->where(...)->update(...)).
 * Before an update or delete the affected rows are read, so the log holds what the record was before.
 */
class AuditedQueryBuilder extends Builder
{
    public function insert(array $values)
    {
        $result = parent::insert($values);

        if ($result && ($table = $this->auditTable()) && ! AuditWriter::skips($table) && ! empty($values)) {
            AuditWriter::inserted($table, $values);
        }

        return $result;
    }

    public function insertGetId(array $values, $sequence = null)
    {
        $id = parent::insertGetId($values, $sequence);

        if (($table = $this->auditTable()) && ! AuditWriter::skips($table)) {
            AuditWriter::inserted($table, $values, $id);
        }

        return $id;
    }

    public function insertOrIgnore(array $values)
    {
        $result = parent::insertOrIgnore($values);

        if ($result && ($table = $this->auditTable()) && ! AuditWriter::skips($table) && ! empty($values)) {
            AuditWriter::inserted($table, $values);
        }

        return $result;
    }

    public function upsert(array $values, array|string $uniqueBy, ?array $update = null)
    {
        $result = parent::upsert($values, $uniqueBy, $update);

        if ($result && ($table = $this->auditTable()) && ! AuditWriter::skips($table) && ! empty($values)) {
            AuditWriter::inserted($table, $values, null, 'upserted');
        }

        return $result;
    }

    public function update(array $values)
    {
        $table = $this->auditTable();
        $before = ($table && ! AuditWriter::skips($table)) ? $this->auditRows() : null;

        $affected = parent::update($values);

        if ($before !== null && $affected && ! empty($before)) {
            AuditWriter::updated($table, $before, $this->auditRowsAfter($table, $before), $values, (int) $affected);
        }

        return $affected;
    }

    public function delete($id = null)
    {
        // Same as the parent: deleting by id adds that condition first, so the rows read below are the right ones
        if (! is_null($id)) {
            $this->where($this->from.'.id', '=', $id);
        }

        $table = $this->auditTable();
        $before = ($table && ! AuditWriter::skips($table)) ? $this->auditRows() : null;

        $affected = parent::delete();

        if ($before !== null && $affected && ! empty($before)) {
            AuditWriter::deleted($table, $before, (int) $affected);
        }

        return $affected;
    }

    public function truncate()
    {
        $table = $this->auditTable();

        parent::truncate();

        if ($table && ! AuditWriter::skips($table)) {
            AuditWriter::truncated($table);
        }
    }

    // ------------------------------------------------------------------

    /** The plain table name, or null when this is not a simple single-table statement. */
    private function auditTable(): ?string
    {
        if (! is_string($this->from) || $this->from === '') {
            return null;
        }

        return trim(preg_split('/\s+as\s+/i', $this->from)[0], '` ');
    }

    /** The rows this statement is about to change, read with the same conditions (capped). */
    private function auditRows(): ?array
    {
        try {
            if (! empty($this->groups) || ! empty($this->havings) || ! empty($this->unions)) {
                return null;
            }

            $parts = preg_split('/\s+as\s+/i', $this->from);
            $reference = trim($parts[1] ?? $parts[0], '` ');

            $clone = clone $this;
            $clone->columns = [$reference.'.*'];
            $clone->limit = min($this->limit ?? AuditWriter::maxRows(), AuditWriter::maxRows());
            $clone->lock = null;

            return $clone->get()->map(fn ($row) => (array) $row)->all();
        } catch (\Throwable $e) {
            Log::warning('Audit: could not read rows before a change on '.$this->from.': '.$e->getMessage());

            return null;
        }
    }

    /** The same rows after the change, keyed by id (only when the table has an id column). */
    private function auditRowsAfter(string $table, array $before): array
    {
        try {
            $ids = array_values(array_filter(array_column($before, 'id'), fn ($id) => $id !== null));
            if (empty($ids)) {
                return [];
            }

            return $this->newQuery()->from($table)->whereIn('id', $ids)->get()
                ->mapWithKeys(fn ($row) => [((array) $row)['id'] => (array) $row])->all();
        } catch (\Throwable $e) {
            Log::warning('Audit: could not re-read rows after a change on '.$table.': '.$e->getMessage());

            return [];
        }
    }
}
