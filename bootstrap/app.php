<?php

use App\Http\Middleware\VerifyIntegrationSignature;
use App\Http\Middleware\VerifySageConnectorApiKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Sage Connector API routes
            Route::middleware('api')
                ->group(base_path('routes/api_sage_connector.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'verify.integration.signature' => VerifyIntegrationSignature::class,
            'sage.connector' => VerifySageConnectorApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
