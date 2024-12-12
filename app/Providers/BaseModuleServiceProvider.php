<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class BaseModuleServiceProvider extends ServiceProvider
{
  protected string $name = ''; // Modül adı
  protected string $theme = ''; // Tema adı

  /**
   * Boot işlemleri.
   */
  public function boot(): void
  {
    $this->registerThemeAndViews();
    $this->registerModuleViewNamespaces();
  }

  /**
   * Tema ve view yollarını dinamik olarak ayarlar.
   */
  protected function registerThemeAndViews(): void
  {
    // Aktif tema adı
    $theme = $this->theme ?: config('app.theme');
    Config::set('app.theme', $theme);

    // Modül view yolları
    $themePath = resource_path("views/themes/{$theme}");
    $modulePaths = glob(base_path("Modules/*/Resources/views/themes/{$theme}"), GLOB_ONLYDIR);

    // Laravel View Finder ile view yollarını ayarla
    $viewFinder = app('view.finder');
    $viewPaths = array_merge($modulePaths, [$themePath], $viewFinder->getPaths());
    $viewFinder->setPaths($viewPaths);

    // Varsayılan view namespace'ini yükle
    $this->loadViewsFrom(module_path($this->name, 'Resources/views'), $this->name);

    // Blade Cache'i tema bazlı ayarla
    $compiledPath = storage_path("framework/views/{$theme}");
    if (!is_dir($compiledPath)) {
      mkdir($compiledPath, 0755, true);
    }
    Config::set('view.compiled', $compiledPath);

    // Loglama
    //logger()->info("Views and theme paths registered for module: {$this->name}", $viewFinder->getPaths());
  }

  /**
   * Modül view namespace'lerini dinamik olarak ekle.
   */
  protected function registerModuleViewNamespaces(): void
  {
    $currentTheme = config('app.theme', config('themes.default_theme'));
    $modules = app('modules')->allEnabled();

    foreach ($modules as $module) {
      $viewPath = str_replace(
        '{module}',
        $module->getName(),
        config("themes.themes.$currentTheme.views_path")
      );

      if (is_dir(base_path($viewPath))) {
        view()->addNamespace($module->getLowerName(), base_path($viewPath));
      }
    }
  }
}
