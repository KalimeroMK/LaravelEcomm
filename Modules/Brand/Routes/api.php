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
Route::apiResource('brand', BrandController::class)->except('update');
Route::post('brand/{id}', [BrandController::class, 'update'])->name('brand.update');