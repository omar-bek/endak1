<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'provider' => \App\Http\Middleware\ProviderMiddleware::class,
            'user.type.terms' => \App\Http\Middleware\EnsureUserTypeAndTermsAccepted::class,
            'api.token' => \App\Http\Middleware\ApiTokenMiddleware::class,
        ]);

        $middleware->web([
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Enable CORS for API routes
        $middleware->api([
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
