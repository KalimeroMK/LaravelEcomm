<?php

    use App\Http\Controllers\Auth\LoginController;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Route;
    use Modules\Front\Http\Controllers\FrontController;
    use Spatie\Feed\Http\FeedController;
    use UniSharp\LaravelFilemanager\Lfm;

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
    // NewsLetter
    Route::post('/subscribe', [FrontController::class, 'subscribe'])->name('subscribe');