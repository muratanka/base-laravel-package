<?php

namespace Modules\MultiSite\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
  public function __construct()
  {
    $this->configureViewPaths();
  }

  private function configureViewPaths()
  {
    $theme = config('app.theme');

    if (!$theme) {
      return; // Ana site için hiçbir işlem yapma
    }

    $modulePaths = glob(base_path('Modules/*/Resources/views/themes/' . $theme), GLOB_ONLYDIR);

    $viewPaths = array_merge(
      [$this->normalizePath(module_path('MultiSite', 'Resources/views/themes/' . $theme))],
      array_map([$this, 'normalizePath'], $modulePaths),
      array_map([$this, 'normalizePath'], config('view.paths'))
    );

    // Benzersiz yolları ayarla
    $viewPaths = array_values(array_unique($viewPaths));
    View::getFinder()->setPaths($viewPaths);
  }

  public function index()
  {
    $theme = config('app.theme');

    if (!$theme) {

      return view('welcome');
    }

    return $this->loadView('index');
  }

  protected function loadView(string $view, array $data = [])
  {
    $theme = config('app.theme');

    $module = $this->getModuleName();

    if (!$module) {
      abort(500, 'Module name could not be determined.');
    }




    // Sadece modül adı ve view dosyasını kullanarak çağır
    $themeView = "{$module}::themes.{$theme}.{$view}";

    if (view()->exists($themeView)) {
      $renderedView = view($themeView)->render(); // View'ı render et ve içeriğini al
      return $renderedView; // Render edilmiş view'ı döndür
    }

    if (view()->exists($themeView)) {
      return view($themeView, $data);
    }

    // Varsayılan view dosyasına düş
    $defaultView = "{$module}::default.{$view}";


    if (view()->exists($defaultView)) {

      return view($defaultView, $data);
    }

    Log::warning("View not found: Theme View [{$themeView}] or Default View [{$defaultView}]");
    abort(404, "View [{$themeView}] or [{$defaultView}] not found.");
  }

  protected function getModuleName()
  {
    $namespaceParts = explode('\\', static::class);

    if (in_array('Modules', $namespaceParts)) {
      return $namespaceParts[array_search('Modules', $namespaceParts) + 1];
    }

    return null;
  }

  private function normalizePath(string $path): string
  {
    return strtolower(str_replace('\\', '/', $path));
  }
}
