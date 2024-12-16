<?php

namespace Modules\MultiSite\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\MultiSite\Entities\Site;
use App\Providers\BaseModuleServiceProvider;

class MultiSiteServiceProvider extends BaseModuleServiceProvider
{
    protected string $name = 'MultiSite';
    protected string $theme = 'default';

    public function boot(): void
    {
        parent::boot();

        $this->registerDynamicConfiguration();
    }

    private function registerDynamicConfiguration(): void
    {
        $host = request()->getHost();
        $this->configureBladeCache($host);
        $this->configureLogging($host);
        Log::info('Current Host:', ['host' => $host]);

        $site = Site::on('mysql') // Ana veritabanı bağlantısı
            ->where('domain', $host)
            ->first();

        if (!$site) {
            if ($host === 'localhost') {
                $this->configureDefaultSite($host, true);
            } else {
                abort(404, 'Site not found.');
            }
            return;
        }

        if ($site->type === 'main') {
            $this->configureMainSite($site);
        } elseif ($site->type === 'customer') {
            $this->configureDynamicDatabase($site);
        } else {
            abort(404, 'Invalid site type.');
        }

        $theme = $site->theme ?? $this->theme;
        Config::set('app.theme', $theme);
        $this->configureViewPaths(Config::get('app.theme'));
        Config::set('app.locale', $site->default_language ?? config('app.locale'));

        Log::info('Theme and Locale Configured:', [
            'theme' => Config::get('app.theme'),
            'locale' => Config::get('app.locale'),
        ]);
    }

    private function configureMainSite(Site $site): void
    {
        Config::set('database.default', 'mysql');

        if (!$site->theme) {
            $site->theme = 'default';
        }

        Log::info("Main site configured for domain: {$site->domain}");
    }

    private function configureDynamicDatabase(Site $site): void
    {
        Config::set('database.connections.dynamic', [
            'driver' => 'mysql',
            'host' => $site->db_host ?? env('DB_HOST', '127.0.0.1'),
            'port' => $site->db_port ?? env('DB_PORT', '3306'),
            'database' => $site->db_name,
            'username' => $site->db_user ?? env('DB_USERNAME', 'root'),
            'password' => $site->db_password ?? env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ]);

        Config::set('database.default', 'dynamic');

        try {
            DB::connection('dynamic')->getPdo();
            Log::info("Dynamic database connection successful for: {$site->db_name}");
        } catch (\Exception $e) {
            Log::error("Dynamic database connection failed for {$site->db_name}: " . $e->getMessage());
            abort(500, 'Database connection failed.');
        }
    }

    private function configureDefaultSite(string $host): void
    {
        Log::warning("No site configuration found for host: {$host}");

        $site = new Site([
            'domain' => $host,
            'type' => 'main',
            'theme' => 'default',
            'default_language' => 'en',
        ]);

        $this->configureMainSite($site);
    }

    private function configureBladeCache(string $domain): void
    {
        $compiledPath = storage_path("framework/views/{$domain}");
        if (!is_dir($compiledPath)) {
            mkdir($compiledPath, 0755, true);
        }
        Config::set('view.compiled', $compiledPath);
        Log::info("Blade cache path configured: {$compiledPath}");
    }

    private function configureViewPaths(string $theme): void
    {
        $viewFinder = app('view.finder');

        $viewPaths = [
            module_path($this->name, "Resources/views/themes/{$theme}"),
            resource_path("views/themes/{$theme}")
        ];

        foreach ($viewPaths as $path) {
            if (is_dir($path)) {
                $viewFinder->prependLocation($path);
            }
        }

        Log::info('View paths configured:', $viewPaths);
    }

    private function configureLogging(string $domain): void
    {
        $logPath = storage_path("logs/{$domain}.log");
        Config::set('logging.channels.daily.path', $logPath);
        Log::info("Log file configured for: {$domain}");
    }
}
