<?php
return [
    'testmode' => env('PAYNOW_TESTMODE', true),
    "default_email" => env('PAYNOW_DEFAULT_EMAIL', 'benson.misi@outlook.com'),
    'return_url' => env('PAYNOW_RETURN_URL', 'http://localhost:8000'),
    'result_url' => env('PAYNOW_RESULT_URL', 'http://localhost:8000'),
];
