<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Global HTTP middleware stack.
     */
    protected $middleware = [

        // Tambahkan middleware Anda di sini
        \App\Http\Middleware\CorsMiddleware::class,
        // Ladumor\LaravelPwa\PWAServiceProvider::class,

    ];

    protected $routeMiddleware = [
        // middleware lain...
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        // 'LaravelPwa' => \Ladumor\LaravelPwa\LaravelPwa::class,

    ];
}
