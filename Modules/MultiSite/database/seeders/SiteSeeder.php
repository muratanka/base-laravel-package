<?php

namespace Modules\MultiSite\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\MultiSite\Entities\Site;

class SiteSeeder extends Seeder
{
    public function run()
    {
        // Ana site
        Site::create([
            'domain' => 'site1.test',
            'type' => 'main',
            'theme' => null,
            'default_language' => 'en',
        ]);

        // Müşteri sitesi
        Site::create([
            'domain' => 'customer1.test',
            'type' => 'customer',
            'theme' => 'theme1',
            'default_language' => 'en',
        ]);
    }
}
