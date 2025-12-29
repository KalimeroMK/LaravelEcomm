<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\Api\TenantController;

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

// Tenant management routes
Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::apiResource('tenants', TenantController::class)->names('api.tenants');
});

// Legacy route for current tenant info
Route::middleware(['auth:sanctum'])->name('api.')->group(function (): void {
    Route::get('tenant', fn (Request $request) => $request->user())->name('tenant');
});
