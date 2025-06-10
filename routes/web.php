<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\MagicLoginController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\FrontController;
use Modules\User\Http\Controllers\Api\AuthController;
use Spatie\Feed\Http\FeedController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('feed', FeedController::class)->name('feeds.main');
Auth::routes();

Route::post('/magic/send', 'MagicLoginConAuth\troller@sendToken')->name('magic.send');
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

Route::get('lang/{lang}', [LanguageController::class, 'switchLang'])->name('lang.switch');

// Attribute Group CRUD
Route::resource('attribute-groups', Modules\Attribute\Http\Controllers\AttributeGroupController::class);

// Banner frontend display and impression tracking
Route::get('banners', [FrontController::class, 'banners'])->name('front.banners');
Route::post('banner/impression/{id}', [FrontController::class, 'bannerImpression'])->name('banner.impression');
