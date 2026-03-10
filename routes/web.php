<?php

declare(strict_types=1);

use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Language\Models\Language;
use Modules\User\Http\Controllers\Api\AuthController;
use Modules\User\Http\Controllers\MagicLoginController;
use Spatie\Feed\Http\FeedController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Non-localized routes and root redirect. All frontend routes with
| locale prefix are defined in Modules/Front/Routes/web.php
|
*/

// Non-localized routes (must be before localized routes)
Route::get('feed', FeedController::class)->name('feeds.main');
Auth::routes();

// Magic Login
Route::post('/magic/send', [MagicLoginController::class, 'sendToken'])->name('magic.send');
Route::get('/magic/login/{token}', [MagicLoginController::class, 'login'])->name('magic.login');
Route::get('/magic/generate', [MagicLoginController::class, 'showLoginForm'])->name('magic-login.show-login-form');

// Socialite
Route::get('/login/{social}', [AuthController::class, 'socialLogin'])->where(
    'social',
    'twitter|facebook|linkedin|google|github|bitbucket'
);
Route::get('/login/{social}/callback', [AuthController::class, 'handleProviderCallback'])->where(
    'social',
    'twitter|facebook|linkedin|google|github|bitbucket'
);

// Language switch route (for frontend)
Route::get('language/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');

/**
 * Safely get the default language code, handling database unavailability (e.g., during testing)
 */
if (!function_exists('getSafeDefaultLocale')) {
    function getSafeDefaultLocale(): string
    {
        try {
            return Language::getDefaultCode();
        } catch (\Illuminate\Database\QueryException) {
            return config('app.locale', 'en');
        }
    }
}

// Redirect root to default locale
Route::get('/', function () {
    return redirect('/' . getSafeDefaultLocale());
});

// Set default locale for URL generation
URL::defaults(['locale' => getSafeDefaultLocale()]);
