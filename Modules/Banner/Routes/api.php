<?php

    use Illuminate\Support\Facades\Route;
    use Modules\Banner\Http\Controllers\Api\BannerController;

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

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('banners', [BannerController::class, 'index'])->name('index');
        Route::post('banners', [BannerController::class, 'store'])->name('store');
        Route::get('banners/{id}', [BannerController::class, 'show'])->name('show');
        Route::patch('banners/{id}', [BannerController::class, 'update'])->name('update');
        Route::delete('banners/{id}', [BannerController::class, 'destroy'])->name('destroy');
    });
