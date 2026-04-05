<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Language\Http\Controllers\LanguageController;

// Admin routes (require admin permission)
Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:admin'],
], function (): void {
    Route::resource('languages', LanguageController::class)->names([
        'index' => 'admin.languages.index',
        'create' => 'admin.languages.create',
        'store' => 'admin.languages.store',
        'edit' => 'admin.languages.edit',
        'update' => 'admin.languages.update',
        'destroy' => 'admin.languages.destroy',
    ]);
});

