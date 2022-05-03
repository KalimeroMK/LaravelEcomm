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

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('brands', [BrandController::class, 'index'])->name('index');
        Route::post('brands', [BrandController::class, 'store'])->name('store');
        Route::get('brands/{id}', [BrandController::class, 'show'])->name('show');
        Route::patch('brands/{id}', [BrandController::class, 'update'])->name('update');
        Route::delete('brands/{id}', [BrandController::class, 'destroy'])->name('destroy');
    });
