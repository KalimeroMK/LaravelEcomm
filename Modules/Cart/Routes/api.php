<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\Api\CartController;

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

// API Routes
Route::apiResource('carts', CartController::class)
    ->names('api.carts');

// Cart Additional Routes
Route::prefix('carts')->group(function (): void {
    Route::post('add/{slug}', [CartController::class, 'addToCart'])->name('api.carts.add');
    Route::post('update-items', [CartController::class, 'updateCartItems'])->name('api.carts.update-items');
});
