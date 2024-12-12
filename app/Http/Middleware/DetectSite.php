<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\View\FileViewFinder;



class DetectSite
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $sites = config('sites.sites');
        $defaultSite = config('sites.default_site');

        $currentSite = collect($sites)->firstWhere('domain', $host) ?? $sites[$defaultSite];

        if (!$currentSite) {
            abort(404, 'Site configuration not found for domain: ' . $host);
        }

        Log::info('Current Site:', $currentSite);

        // Veritabanı bağlantısını ayarla
        Config::set('database.default', $currentSite['db_connection']);

        // Aktif temayı ayarla
        Config::set('app.theme', $currentSite['theme']);



        return $next($request);
    }
}
