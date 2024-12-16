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
    $this->registerMigrations();
  }

  /**
   * Tema ve view yollarını dinamik olarak ayarlar.
   */
  protected function registerThemeAndViews(): void
  {
    $theme = $this->theme ?: config('app.theme');
    Config::set('app.theme', $theme);

    // Tema yollarını ayarla
    $themePath = resource_path("views/themes/{$theme}");
    $modulePaths = glob(base_path("Modules/*/Resources/views/themes/{$theme}"), GLOB_ONLYDIR);

    // View Finder ile yolları ekle
    $viewFinder = app('view.finder');
    $viewPaths = array_merge(
      [$themePath], // Tema genel yolları
      $modulePaths, // Modül temaları
      $viewFinder->getPaths() // Mevcut yollar
    );

    $viewFinder->setPaths(array_values(array_unique($viewPaths)));

    // Varsayılan view namespace'ini ekle
    if ($this->name) {
      $this->loadViewsFrom(module_path($this->name, 'Resources/views'), $this->name);
    }

    // Blade cache yolunu tema bazlı yap
    $compiledPath = storage_path("framework/views/{$theme}");
    if (!is_dir($compiledPath)) {
      mkdir($compiledPath, 0755, true);
    }
    Config::set('view.compiled', $compiledPath);
  }

  /**
   * Modül migration dosyalarını otomatik yükler.
   */
  protected function registerMigrations(): void
  {
    if ($this->name) {
      $migrationsPath = module_path($this->name, 'database/migrations');
      if (is_dir($migrationsPath)) {
        $this->loadMigrationsFrom($migrationsPath);
      }
    }
  }
}
