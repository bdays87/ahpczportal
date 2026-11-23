<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Whatsapp\WhatsappWebhookProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Meta's WhatsApp webhook.
 *   GET  /api/whatsapp/webhook  - one-time check when you register the webhook in Meta
 *   POST /api/whatsapp/webhook  - every incoming message
 */
class WhatsappWebhookController extends Controller
{
    public function verify(Request $request)
    {
        $expected = (string) config('services.whatsapp.verify_token');

        if (config('services.whatsapp.enabled')
            && $expected !== ''
            && $request->query('hub_mode') === 'subscribe'
            && hash_equals($expected, (string) $request->query('hub_verify_token'))) {
            return response((string) $request->query('hub_challenge'), 200)->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    public function receive(Request $request, WhatsappWebhookProcessor $processor)
    {
        if (! config('services.whatsapp.enabled')) {
            return response()->json(['error' => 'disabled'], 404);
        }

        // Only accept calls really signed by Meta with our app secret
        $secret = (string) config('services.whatsapp.app_secret');
        if ($secret === '') {
            Log::error('WhatsApp webhook refused: WHATSAPP_APP_SECRET is not set');

            return response()->json(['error' => 'not configured'], 500);
        }

        $expected = 'sha256='.hash_hmac('sha256', $request->getContent(), $secret);
        if (! hash_equals($expected, (string) $request->header('X-Hub-Signature-256'))) {
            Log::warning('WhatsApp webhook: bad signature', ['ip' => $request->ip()]);

            return response()->json(['error' => 'invalid signature'], 403);
        }

        $payload = $request->json()->all();

        // Answer Meta straight away (it retries if we are slow), then do the work.
        dispatch(fn () => $processor->process($payload))->afterResponse();

        return response()->json(['status' => 'ok']);
    }
}
