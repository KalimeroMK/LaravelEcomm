<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\MagicLoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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

Route::get('feed', FeedController::class)->name("feeds.main");
Auth::routes();

Route::post('/magic/send', 'MagicLoginConAuth\troller@sendToken')->name('magic.send');
Route::post('/magic/send', [MagicLoginController::class, 'sendToken'])->name('magic.send');
Route::get('/magic/login/{token}', [MagicLoginController::class, 'login'])->name('magic.login');
Route::get('/magic/generate', [MagicLoginController::class, 'showLoginForm'])->name('magic-login.show-login-form');
// Socialite
Route::get('/login/{social}', [AuthController::class, 'socialLogin'])->where('social',
    'twitter|facebook|linkedin|google|github|bitbucket');
Route::get('/login/{social}/callback', [AuthController::class, 'handleProviderCallback'])->where('social',
    'twitter|facebook|linkedin|google|github|bitbucket');