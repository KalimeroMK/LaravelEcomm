<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\User\Http\Controllers\Api\AuthController;
use Modules\User\Http\Controllers\Api\UserAddressController;
use Modules\User\Http\Controllers\Api\UserController;

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
// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);

    // User management routes
    Route::apiResource('users', UserController::class)->names('api.users');
    
    // User Address routes
    Route::apiResource('user/addresses', UserAddressController::class)->names('api.user.addresses');
    Route::prefix('user/addresses')->group(function (): void {
        Route::post('{address}/default', [UserAddressController::class, 'setDefault'])->name('api.user.addresses.default');
        Route::get('default/shipping', [UserAddressController::class, 'defaultShipping'])->name('api.user.addresses.default-shipping');
        Route::get('default/billing', [UserAddressController::class, 'defaultBilling'])->name('api.user.addresses.default-billing');
    });
});
