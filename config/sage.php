<?php

/**
 * Connection details for the local SageBridge service (see
 * sageintegration/SageBridge) that talks to Sage Evolution on your behalf.
 *
 * Nothing pushes to Sage until `enabled` is true — flip SAGE_INTEGRATION_ENABLED
 * on in .env only once you've filled in the bridge's Invoice/Receipt stubs and
 * tested them by hand (see sageintegration/SageBridge/README.md).
 */
return [

    'enabled' => env('SAGE_INTEGRATION_ENABLED', false),

    'base_url' => env('SAGE_BRIDGE_URL', 'http://127.0.0.1:8990'),

    'api_key' => env('SAGE_BRIDGE_API_KEY'),

    'timeout' => env('SAGE_BRIDGE_TIMEOUT', 15),

    /**
     * GL account code that invoice lines post against in Sage. This portal
     * doesn't sell stock — every application/registration fee line posts as a
     * non-stock line against this revenue account. Confirm the code with
     * whoever administers the Evolution company (Setup > GL Accounts).
     */
    'default_gl_account' => env('SAGE_DEFAULT_GL_ACCOUNT'),

];
