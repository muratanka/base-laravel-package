<?php

use Illuminate\Support\Facades\Route;
use Modules\MultiSite\Http\Controllers\MultiSiteController;
use Modules\MultiSite\Http\Controllers\BaseController;

/*
|--------------------------------------------------------------------------
| Multi-Site Routes
|--------------------------------------------------------------------------
|
| Burada Multi-Site modülüne özel rotalar tanımlanır.
| Hem ana domain hem de müşteri domainlerine uygun yönlendirmeler yapılır.
|
*/

// Ana domain veya müşteri domainine gelen root istekleri için BaseController
Route::get('/', [BaseController::class, 'index']);

// Diğer işlemler için MultiSiteController
//Route::resource('multisite', MultiSiteController::class)->names('multisite');
