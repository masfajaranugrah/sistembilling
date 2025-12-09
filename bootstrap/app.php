<?php

use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\RoleMiddleware;
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
            // Load channels manually after custom broadcasting route
            require __DIR__.'/../routes/channels.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // ? Middleware global (dijalankan di semua web routes)
        $middleware->web([
            LocaleMiddleware::class,
        ]);

        // ? Alias middleware khusus (dipanggil dengan nama)
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'auth.redirect' => \App\Http\Middleware\RedirectIfAuthenticatedCustom::class,
            'session.check' => \App\Http\Middleware\SessionCheck::class,

        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
