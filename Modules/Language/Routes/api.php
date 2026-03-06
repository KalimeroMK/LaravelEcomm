<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Language\Http\Controllers\Api\LanguageController;

// Public API routes
Route::get('languages', [LanguageController::class, 'index'])->name('api.languages.index');
Route::get('languages/current', [LanguageController::class, 'current'])->name('api.languages.current');
Route::get('languages/default', [LanguageController::class, 'default'])->name('api.languages.default');
Route::post('languages/set-locale', [LanguageController::class, 'setLocale'])->name('api.languages.set-locale');

// Admin API routes (require admin permission)
Route::group([
    'middleware' => ['auth:sanctum', 'role:admin'],
    'prefix' => 'admin',
], function (): void {
    Route::get('languages/all', [LanguageController::class, 'adminIndex'])->name('api.admin.languages.index');
    Route::post('languages', [LanguageController::class, 'store'])->name('api.admin.languages.store');
    Route::put('languages/{language}', [LanguageController::class, 'update'])->name('api.admin.languages.update');
    Route::delete('languages/{language}', [LanguageController::class, 'destroy'])->name('api.admin.languages.destroy');
});
