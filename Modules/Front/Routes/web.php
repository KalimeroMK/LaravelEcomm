<?php

declare(strict_types=1);

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

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\FrontController;
use Modules\Front\Http\Controllers\PlaceholderImageController;

Route::get('placeholder/image', PlaceholderImageController::class)->name('front.placeholder.image');
Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/about-us', [FrontController::class, 'aboutUs'])->name('front.about-us');
Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
Route::post('/contact/message', [FrontController::class, 'messageStore'])->name('front.store-message');
Route::get('product-detail/{slug}', [FrontController::class, 'productDetail'])->name('front.product-detail');
Route::post('/product/search', [FrontController::class, 'productSearch'])->name('front.product-search');
Route::get('/product-cat/{slug}', [FrontController::class, 'productCat'])->name('front.product-cat');
Route::get('/product-brand/{slug}', [FrontController::class, 'productBrand'])->name('front.product-brand');
Route::get('/blog', [FrontController::class, 'blog'])->name('front.blog');
Route::get('/blog-detail/{slug}', [FrontController::class, 'blogDetail'])->name('front.blog-detail');
Route::get('/blog/search', [FrontController::class, 'blogSearch'])->name('front.blog-search');
Route::post('/blog/filter', [FrontController::class, 'blogFilter'])->name('front.blog-filter');
Route::get('blog-cat/{slug}', [FrontController::class, 'blogByCategory'])->name('front.blog-by-category');
Route::get('blog-tag/{slug}', [FrontController::class, 'blogByTag'])->name('front.blog-by-tag');
Route::get('/product/deal', [FrontController::class, 'productDeal'])->name('front.product-deal');
Route::post('cart/order', [FrontController::class, 'store'])->name('front.cart.order');
// Auth protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', function () {
        return redirect()->route('cart-list');
    })->name('front.cart');
    Route::get('/cart-list', function () {
        return view(theme_view('pages.cart'));
    })->name('cart-list');
    Route::get('/checkout', [\Modules\Cart\Http\Controllers\CartController::class, 'checkout'])->name('checkout');
    Route::get('cart-delete/{id}', [\Modules\Cart\Http\Controllers\CartController::class, 'cartDelete'])->name('cart-delete');
    Route::post('cart-update', [\Modules\Cart\Http\Controllers\CartController::class, 'cartUpdate'])->name('cart-update');
});
Route::match(['get', 'post'], '/filter', [FrontController::class, 'productFilter'])->name('front.product-filter');
Route::get('/product-lists', [FrontController::class, 'productLists'])->name('front.product-lists');
Route::get('/product-grids', [FrontController::class, 'productGrids'])->name('front.product-grids');
Route::get('/bundle', [FrontController::class, 'bundles'])->name('front.bundles');
Route::get('/bundle/{slug}', [FrontController::class, 'bundleDetail'])->name('front.bundle-detail');
Route::get('/page/{slug}', [FrontController::class, 'pages'])->name('front.pages');

// User Orders
Route::middleware(['auth'])->group(function () {
    Route::get('/my-orders', [FrontController::class, 'myOrders'])->name('front.my-orders');
    Route::get('/my-orders/{order}', [FrontController::class, 'orderDetail'])->name('front.order-detail');
    Route::post('/my-orders/{order}/reorder', [FrontController::class, 'reorder'])->name('front.reorder');
});

// Recently Viewed Products
Route::get('/recently-viewed', [FrontController::class, 'recentlyViewed'])->name('front.recently-viewed');

// Advanced Search and Recommendations Routes
Route::get('/advanced-search', [FrontController::class, 'advancedSearch'])->name('front.advanced-search');
Route::get('/search-suggestions', [FrontController::class, 'searchSuggestions'])->name('front.search-suggestions');
Route::get('/recommendations', [FrontController::class, 'recommendations'])->name('front.recommendations');
Route::get('/related-products/{slug}', [FrontController::class, 'relatedProducts'])->name('front.related-products');
Route::get('/enhanced-wishlist', [FrontController::class, 'enhancedWishlist'])->name('front.enhanced-wishlist');

// NewsLetter
Route::post('/subscribe', [Modules\Front\Http\Controllers\Api\FrontController::class, 'subscribe'])->name('subscribe');
Route::get('/validation/{token}', [FrontController::class, 'verifyNewsletter'])->name('validation');
Route::get('/delete/{token}', [FrontController::class, 'deleteNewsletter'])->name('delete-newsletter');
