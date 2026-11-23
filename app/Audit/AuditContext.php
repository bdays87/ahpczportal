<?php

namespace App\Audit;

use Illuminate\Support\Facades\Auth;

/**
 * Works out who is doing something and from where, for the audit log.
 */
class AuditContext
{
    /** @var array<int|string, array> */
    private static array $actors = [];

    /** @return array{user_id:?int,username:?string,user_name:?string} */
    public static function actor($user = null): array
    {
        try {
            if ($user === null) {
                $guard = Auth::guard();
                if (app()->runningInConsole() && ! (method_exists($guard, 'hasUser') && $guard->hasUser())) {
                    return ['user_id' => null, 'username' => 'console', 'user_name' => 'System (command line)'];
                }
                $user = Auth::user();
            }
        } catch (\Throwable $e) {
            $user = null;
        }

        if (! $user) {
            return ['user_id' => null, 'username' => 'guest', 'user_name' => null];
        }

        if (isset(self::$actors[$user->id])) {
            return self::$actors[$user->id];
        }

        // What the person signs in with: username, else registration number (practitioners), else email
        $login = $user->username ?? null;
        if (! $login) {
            try {
                $login = $user->customer?->customer?->regnumber;
            } catch (\Throwable $e) {
                $login = null;
            }
        }

        return self::$actors[$user->id] = [
            'user_id' => $user->id,
            'username' => $login ?: $user->email,
            'user_name' => trim(($user->name ?? '').' '.($user->surname ?? '')) ?: null,
        ];
    }

    /** @return array{ip:?string,http_method:?string,url:?string,user_agent:?string} */
    public static function request(): array
    {
        if (app()->runningInConsole()) {
            return ['ip' => null, 'http_method' => null, 'url' => null, 'user_agent' => null];
        }

        $request = request();

        return [
            'ip' => $request->ip(),
            'http_method' => $request->method(),
            'url' => mb_substr($request->fullUrl(), 0, 500),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
        ];
    }

    /** A readable name for what was being done, e.g. "Admin\Banktransactions::saveClaimCustomer". */
    public static function operation(): ?string
    {
        if (app()->runningInConsole()) {
            $command = $_SERVER['argv'][1] ?? null;

            return 'Command: '.($command ?: 'console');
        }

        $request = request();

        if ($request->is('api/whatsapp/*')) {
            return 'WhatsApp bot';
        }

        $components = $request->input('components');
        if ($request->hasHeader('X-Livewire') && is_array($components) && ! empty($components[0])) {
            $snapshot = json_decode($components[0]['snapshot'] ?? '[]', true);
            $name = $snapshot['memo']['name'] ?? null;
            $calls = collect($components[0]['calls'] ?? [])->pluck('method')
                ->filter(fn ($m) => is_string($m) && ! str_starts_with($m, '$'))->implode(', ');
            if ($name) {
                return mb_substr($name.($calls ? '::'.$calls : ''), 0, 255);
            }
        }

        $route = $request->route();

        return mb_substr(($route?->getName()) ?: $request->method().' '.$request->path(), 0, 255);
    }
}
