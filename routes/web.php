<?php

declare(strict_types=1);

use App\Http\Controllers\LanguageController;
use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\FrontController;
use Modules\User\Http\Controllers\Api\AuthController;
use Modules\User\Http\Controllers\MagicLoginController;
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

// Non-localized routes (must be before localized routes)
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

Route::get('language/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');

// Redirect root to default locale
Route::get('/', function () {
    return redirect('/' . \Modules\Language\Models\Language::getDefaultCode());
});

// Localized routes with locale prefix: /en/, /mk/, /de/
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
    'middleware' => [LocaleMiddleware::class],
], function (): void {
    // Attribute Group CRUD
    Route::resource('attribute-groups', Modules\Attribute\Http\Controllers\AttributeGroupController::class);

    // Banner frontend display and impression tracking
    Route::get('banners', [FrontController::class, 'banners'])->name('front.banners');
    Route::post('banner/impression/{id}', [FrontController::class, 'bannerImpression'])->name('banner.impression');
    
    // Product routes
    Route::get('products', [FrontController::class, 'products'])->name('front.products');
    Route::get('products/{slug}', [FrontController::class, 'productDetail'])->name('front.product.detail');
    
    // Category routes
    Route::get('categories', [FrontController::class, 'categories'])->name('front.categories');
    Route::get('categories/{slug}', [FrontController::class, 'categoryDetail'])->name('front.category.detail');
    
    // Page routes
    Route::get('pages/{slug}', [FrontController::class, 'page'])->name('front.page');
    
    // Post routes
    Route::get('blog', [FrontController::class, 'blog'])->name('front.blog');
    Route::get('blog/{slug}', [FrontController::class, 'postDetail'])->name('front.post.detail');
    
    // Cart routes
    Route::get('cart', [FrontController::class, 'cart'])->name('front.cart');
    Route::post('cart/add', [FrontController::class, 'cartAdd'])->name('front.cart.add');
    
    // Checkout routes
    Route::get('checkout', [FrontController::class, 'checkout'])->name('front.checkout');
    Route::post('checkout', [FrontController::class, 'checkoutProcess'])->name('front.checkout.process');
    
    // Home
    Route::get('/', [FrontController::class, 'index'])->name('front.index');
});
