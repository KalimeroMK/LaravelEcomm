<?php

use Illuminate\Support\Facades\Route;
use Modules\Brand\Http\Controllers\Api\BrandController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::apiResource('brands', BrandController::class)
    ->except('update')
    ->names([
        'index' => 'api.brand.index',
        'store' => 'api.brand.store',
        'show' => 'api.brand.show',
        'destroy' => 'api.brand.destroy',
        'create' => 'api.brand.create', // if you're using forms for APIs
    ]);
Route::post('brands/{id}', [BrandController::class, 'update'])->name('api.brand.update');
