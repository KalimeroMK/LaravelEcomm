<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\TranslationController;

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
