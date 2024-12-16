<?php

namespace Modules\MultiSite\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DetectSite
{
  public function handle($request, Closure $next)
  {
    $host = $request->getHost();
    Log::info('DetectSite Middleware Started for Host:', ['host' => $host]);

    // Temanın ayarlı olup olmadığını kontrol et
    $theme = Config::get('app.theme');
    if (!$theme) {
      abort(404, 'Theme not configured.');
    }

    return $next($request);
  }
}
