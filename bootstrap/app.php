<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Needed for fly.io
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdminAccessMiddleware::class,
            'honeypot' => \Spatie\Honeypot\ProtectAgainstSpam::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
