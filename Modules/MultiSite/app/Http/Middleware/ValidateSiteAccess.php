<?php

namespace Modules\MultiSite\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class ValidateSiteAccess
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();
        $theme = Config::get('app.theme');

        Log::info("Validating site access for host: {$host}");

        // Ana domain erişim kontrolü
        if (!$theme || ($theme === 'main' && $host !== 'localhost')) {
            Log::warning("Unauthorized site access for host: {$host}");
            abort(403, 'Unauthorized site access');
        }

        return $next($request);
    }
}
