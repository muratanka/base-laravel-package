<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;


class DetectSite
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost(); // Gelen isteğin domain veya subdomain adresini alır.
        $sites = config('sites.sites');
        $defaultSite = config('sites.default_site');

        // Gelen domain veya subdomain için ayarları bul
        $currentSite = collect($sites)->firstWhere('domain', $host) ?? $sites[$defaultSite];

        if (!$currentSite) {
            Log::error("Site configuration for domain {$host} not found.");
            abort(404, 'Site not configured.');
        }

        // Veritabanı bağlantısını ayarla
        Config::set('database.default', $currentSite['db_connection']);
        Config::set('app.theme', $currentSite['theme']);
        Config::set('app.site', $host);

        return $next($request);
    }
}
