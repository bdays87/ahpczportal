<?php

/**
 * Outbound-facing integration partners — systems other than this portal that
 * are allowed to call the /api/integration/* endpoints (see
 * routes/api.php and App\Http\Middleware\VerifyIntegrationSignature).
 *
 * Each partner authenticates with a pre-shared secret: the caller signs the
 * raw request body with HMAC-SHA256 and sends it as `X-Signature: sha256=...`.
 *
 * Add CPDAPP_INTEGRATION_SECRET=<a long random string> to .env, and configure
 * the exact same value as MLCSCZ_INTEGRATION_SECRET in the cpdapp project.
 */
return [

    'cpdapp' => [
        'secret' => env('CPDAPP_INTEGRATION_SECRET'),
        'description' => 'CPD Platform — practitioner registration-number login (reg-number + OTP)',
    ],

];
