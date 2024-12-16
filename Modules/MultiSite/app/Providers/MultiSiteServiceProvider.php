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
        Log::info('Current Host:', ['host' => $host]);

        $site = Site::on('mysql') // Ana veritabanı bağlantısı
            ->where('domain', $host)
            ->first();

        if (!$site) {
            abort(404, 'Site not found.');
        }

        // Ana site mi, müşteri sitesi mi kontrol et
        if ($site->type === 'main') {
            $this->configureMainSite($site);
        } elseif ($site->type === 'customer') {
            $this->configureDynamicDatabase($site);
        } else {
            abort(404, 'Invalid site type.');
        }

        // **Tema Ayarı**
        $theme = $site->theme ?? $this->theme; // Ana site için varsayılan temayı ayarla
        Config::set('app.theme', $theme);
        Config::set('app.locale', $site->default_language ?? config('app.locale'));

        Log::info('Theme and Locale Configured:', [
            'theme' => Config::get('app.theme'),
            'locale' => Config::get('app.locale'),
        ]);
    }

    private function configureMainSite(Site $site): void
    {
        // Main site varsayılan veritabanını kullanır
        Config::set('database.default', 'mysql');

        // Varsayılan tema kontrolü
        if (!$site->theme) {
            $site->theme = 'default'; // Varsayılan tema
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
}
