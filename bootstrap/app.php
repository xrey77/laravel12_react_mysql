<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            '2fa' => \PragmaRX\Google2FALaravel\Middleware::class,
        ]);        
        // $middleware->validateCsrfTokens(except: [
        //     'stripe/*',
        //     'user/two-factor-authentication',
        //     'crftoken',
        //     'user/two-factor-qr-code'
        // ]);        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
