<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class VerifySageConnectorApiKey
{
    /**
     * Handle an incoming request from SageConnector.
     * 
     * Validates the X-Api-Key header against the configured secret.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-Api-Key');
        $expectedKey = config('services.sage_connector.api_key');

        if (empty($expectedKey)) {
            Log::error('SageConnector: API key not configured in services.sage_connector.api_key');
            return response()->json([
                'error' => 'Server configuration error'
            ], 500);
        }

        if (empty($apiKey)) {
            Log::warning('SageConnector: Request without X-Api-Key header', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
            ]);
            
            return response()->json([
                'error' => 'Missing X-Api-Key header'
            ], 401);
        }

        if (!hash_equals($expectedKey, $apiKey)) {
            Log::warning('SageConnector: Invalid API key', [
                'ip' => $request->ip(),
                'url' => $request->fullUrl(),
                'provided_key' => substr($apiKey, 0, 8) . '...',
            ]);
            
            return response()->json([
                'error' => 'Invalid API key'
            ], 401);
        }

        // API key is valid, proceed
        return $next($request);
    }
}
