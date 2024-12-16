<?php

use Illuminate\Support\Facades\Route;
use Modules\MultiSite\Http\Controllers\BaseController;

// Root rotasını BaseController'a yönlendirin
Route::get('/', [BaseController::class, 'index'])->middleware('web');
