<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Modules\MultiSite\Http\Middleware\DetectSite;

return Application::configure(basePath: dirname(__DIR__))

    // Route tanımlamaları
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // Middleware yapılandırması
    ->withMiddleware(function (Middleware $middleware) {
        // DetectSite Middleware'i başa eklenir
        $middleware->prepend(DetectSite::class);
    })

    // Exception ayarları
    ->withExceptions(function (Exceptions $exceptions) {
        // Özel hata işleme burada tanımlanabilir
    })

    // Uygulama oluşturma
    ->create();
