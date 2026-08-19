<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the outbound-facing /api/integration/* endpoints (see config/integrations.php).
 * The caller signs the raw request body with HMAC-SHA256 using a pre-shared secret and
 * sends it as `X-Signature: sha256=<hex>`. The route parameter {partner} must match a
 * configured key.
 */
class VerifyIntegrationSignature
{
    public function handle(Request $request, Closure $next, string $partner): Response
    {
        $secret = config("integrations.{$partner}.secret");
        $signature = $request->header('X-Signature');

        if (! $secret || ! $signature || ! self::verifySignature($request->getContent(), $signature, $secret)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or missing signature.'], 401);
        }

        return $next($request);
    }

    public static function verifySignature(string $payload, string $signature, string $secret): bool
    {
        $expected = 'sha256='.hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
