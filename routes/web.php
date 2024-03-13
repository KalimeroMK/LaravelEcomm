<?php

use App\Http\Controllers\Auth\LoginController;
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
// Socialite
Route::get('login/{provider}/', [LoginController::class, 'redirect'])->name('login.redirect');
Route::get('login/{provider}/callback/', [LoginController::class, 'Callback'])->name('login.callback');
Route::post('/magic/send', 'MagicLoginConAuth\troller@sendToken')->name('magic.send');
Route::post('/magic/send', [MagicLoginController::class, 'sendToken'])->name('magic.send');
Route::get('/magic/login/{token}', [MagicLoginController::class, 'login'])->name('magic.login');
Route::get('/magic/generate', [MagicLoginController::class, 'showLoginForm'])->name('magic-login.show-login-form');
