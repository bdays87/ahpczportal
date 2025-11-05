<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SSL Certificate Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for SSL certificate handling,
    | particularly for external API calls like Paynow.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | SSL Verification
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificates. Set to false only in development
    | environments if you encounter certificate issues.
    |
    */
    'verify_ssl' => env('SSL_VERIFY', true),

    /*
    |--------------------------------------------------------------------------
    | Custom CA Bundle Path
    |--------------------------------------------------------------------------
    |
    | Path to custom CA bundle file. Leave null to use system defaults
    | or auto-detection.
    |
    */
    'ca_bundle_path' => env('SSL_CA_BUNDLE_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds for establishing SSL connections.
    |
    */
    'connect_timeout' => env('SSL_CONNECT_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Timeout in seconds for completing SSL requests.
    |
    */
    'request_timeout' => env('SSL_REQUEST_TIMEOUT', 30),
];

