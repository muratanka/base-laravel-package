<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
  /**
   * Dinamik olarak temaya ve modüle özgü view döndürür.
   *
   * @param string $view
   * @param array $data
   * @return \Illuminate\View\View
   */
  protected function loadView(string $view, array $data = [])
  {
    $theme = config('app.theme');
    $module = $this->getModuleName();

    if (!$module) {
      abort(500, 'Module name could not be determined.');
    }

    // Temaya ve modüle özgü view yolu
    $themeView = "{$module}::themes.{$theme}.{$view}";
    Log::info("Checking view existence: {$themeView}");
    $defaultView = "{$module}::default.{$view}";
    Log::info("Default view: {$defaultView}");

    if (view()->exists($themeView)) {
      Log::info("View found: {$themeView}");
      return view($themeView, $data);
    } elseif (view()->exists($defaultView)) {
      Log::info("Falling back to default view: {$defaultView}");
      return view($defaultView, $data);
    } else {

      Log::info("View Path Hatalı: ['View Yok']");
    }

    abort(404, "View [{$themeView}] or [{$defaultView}] not found.");
  }

  /**
   * Modül adını belirle.
   *
   * @return string|null
   */
  protected function getModuleName()
  {
    $namespaceParts = explode('\\', static::class);

    if (in_array('Modules', $namespaceParts)) {
      $module = $namespaceParts[array_search('Modules', $namespaceParts) + 1];
      Log::info("Detected Module Name: {$module}");
      return $module;;
    }

    return null;
  }
}
