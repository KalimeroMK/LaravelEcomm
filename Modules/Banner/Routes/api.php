<?php

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

use Illuminate\Support\Facades\Route;
use Modules\Banner\Http\Controllers\Api\BannerController;

Route::apiResource('banner', BannerController::class)->except('update');
Route::post('banner/{id}', [BannerController::class, 'update'])->name('banner.update');
