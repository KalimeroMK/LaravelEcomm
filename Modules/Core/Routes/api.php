<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\SystemController;
use Modules\Core\Http\Controllers\TranslationController;

// System API routes
Route::prefix('system')->name('api.system.')->group(function () {
    // Public: returns status strings only, for external monitoring.
    Route::get('health', [SystemController::class, 'health'])->name('health');

    // Authenticated: reports the exact Laravel and PHP versions plus the
    // environment name, which is free reconnaissance for anyone matching the
    // deployment against known CVEs.
    Route::get('version', [SystemController::class, 'version'])
        ->middleware('auth:sanctum')
        ->name('version');
});

Route::prefix('api/v1')
    ->middleware(['api', 'auth:sanctum'])
    ->group(function () {
        Route::prefix('translations')->group(function () {
            Route::get('model', [TranslationController::class, 'getModelTranslations'])->name('api.translations.model');
            Route::post('model', [TranslationController::class, 'setModelTranslations'])->name('api.translations.set');
            Route::get('missing', [TranslationController::class, 'getMissingTranslations'])->name('api.translations.missing');
            Route::post('auto-translate', [TranslationController::class, 'autoTranslate'])->name('api.translations.auto');
        });
    });
