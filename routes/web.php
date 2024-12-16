<?php

use Illuminate\Support\Facades\Route;
use Modules\MultiSite\Http\Middleware\ValidateSiteAccess;
use Modules\MultiSite\Http\Controllers\BaseController;

// Root rotasına erişim kontrolü için middleware kullanılıyor
Route::middleware(['web', ValidateSiteAccess::class])->group(function () {
  // BaseController'ın index metoduna yönlendirme
  Route::get('/', [BaseController::class, 'index']);
});
