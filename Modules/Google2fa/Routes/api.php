<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Google2fa\Http\Controllers\Api\Google2faController;
use Modules\Google2fa\Http\Controllers\Api\Google2faSettingsController;

/*
||--------------------------------------------------------------------------
|| API Routes
||--------------------------------------------------------------------------
||
|| Here is where you can register API routes for your application. These
|| routes are loaded by the RouteServiceProvider within a group which
|| is assigned the "api" middleware group. Enjoy building your API!
||
*/

// 2FA Routes
Route::prefix('2fa')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('status', [Google2faController::class, 'status'])->name('api.2fa.status');
    Route::post('generate-secret', [Google2faController::class, 'generateSecret'])->name('api.2fa.generate-secret');
    Route::post('enable', [Google2faController::class, 'enable'])->name('api.2fa.enable');
    Route::post('disable', [Google2faController::class, 'disable'])->name('api.2fa.disable');
    Route::post('verify', [Google2faController::class, 'verify'])->name('api.2fa.verify');

    // Recovery Codes Routes
    Route::prefix('recovery-codes')->group(function (): void {
        Route::get('/', [Google2faController::class, 'recoveryCodes'])->name('api.2fa.recovery-codes');
        Route::post('regenerate', [Google2faController::class, 'regenerateRecoveryCodes'])->name('api.2fa.recovery-codes.regenerate');
        Route::post('verify', [Google2faController::class, 'verifyRecoveryCode'])->name('api.2fa.recovery-code.verify');
    });
});

// 2FA Settings Routes (Admin only)
Route::prefix('admin/2fa/settings')->middleware(['auth:sanctum'])->group(function (): void {
    Route::get('/', [Google2faSettingsController::class, 'index'])->name('api.2fa.settings.index');
    Route::put('/', [Google2faSettingsController::class, 'update'])->name('api.2fa.settings.update');
});
