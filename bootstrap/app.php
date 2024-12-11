<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\DetectSite;

return Application::configure(basePath: dirname(__DIR__))

    // Route tanımlamaları
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // Middleware yapılandırması
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware'i doğrudan buraya ekliyoruz.
        $middleware->append(DetectSite::class);
    })

    // Exception ayarları
    ->withExceptions(function (Exceptions $exceptions) {
        // Özel hata işleme burada tanımlanabilir
    })

    // Uygulama oluşturma
    ->create();
