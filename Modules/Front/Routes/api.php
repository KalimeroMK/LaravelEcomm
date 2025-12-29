<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\Api\FrontController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// API Routes
Route::get('/', [FrontController::class, 'index'])->name('api.home');

// Product Routes
Route::get('product-detail/{slug}', [FrontController::class, 'productDetail'])->name('api.product-detail');
Route::get('product-grids', [FrontController::class, 'productGrids'])->name('api.product-grids');
Route::get('product-lists', [FrontController::class, 'productLists'])->name('api.product-lists');
Route::post('/product/search', [FrontController::class, 'productSearch'])->name('api.product.search');
Route::post('/product/filter', [FrontController::class, 'productFilter'])->name('api.product.filter');
Route::get('/product-cat/{slug}', [FrontController::class, 'productCat'])->name('api.product-cat');
Route::get('/product-brand/{slug}', [FrontController::class, 'productBrand'])->name('api.product-brand');
Route::get('/product/deal', [FrontController::class, 'productDeal'])->name('api.product.deal');

// Bundle Routes
Route::get('bundles', [FrontController::class, 'bundles'])->name('api.bundles');
Route::get('bundle-detail/{slug}', [FrontController::class, 'bundleDetail'])->name('api.bundle-detail');

// Blog Routes
Route::get('/blog', [FrontController::class, 'blog'])->name('api.blog');
Route::get('/blog-detail/{slug}', [FrontController::class, 'blogDetail'])->name('api.blog.detail');
Route::get('/blog/search', [FrontController::class, 'blogSearch'])->name('api.blog.search');
Route::post('/blog/filter', [FrontController::class, 'blogFilter'])->name('api.blog.filter');
Route::get('blog-cat/{slug}', [FrontController::class, 'blogByCategory'])->name('api.blog.category');
Route::get('blog-tag/{slug}', [FrontController::class, 'blogByTag'])->name('api.blog.tag');

// Search & Recommendations Routes
Route::post('/search/advanced', [FrontController::class, 'advancedSearch'])->name('api.search.advanced');
Route::get('/search/suggestions', [FrontController::class, 'searchSuggestions'])->name('api.search.suggestions');
Route::get('/recommendations', [FrontController::class, 'recommendations'])->name('api.recommendations');
Route::get('/recommendations/related/{slug}', [FrontController::class, 'relatedProducts'])->name('api.recommendations.related');

// Other Routes
Route::get('pages/{slug}', [FrontController::class, 'pages'])->name('api.pages');
Route::get('banners', [FrontController::class, 'banners'])->name('api.banners');
Route::post('banners/{id}/impression', [FrontController::class, 'bannerImpression'])->name('api.banners.impression');
Route::post('coupon/store', [FrontController::class, 'couponStore'])->name('api.coupon.store');
Route::post('newsletter/subscribe', [FrontController::class, 'subscribe'])->name('api.newsletter.subscribe');
Route::get('newsletter/verify/{token}', [FrontController::class, 'verifyNewsletter'])->name('api.newsletter.verify');
Route::get('newsletter/delete/{token}', [FrontController::class, 'deleteNewsletter'])->name('api.newsletter.delete');
Route::post('message/store', [FrontController::class, 'messageStore'])->name('api.message.store');
Route::get('wishlist/enhanced', [FrontController::class, 'enhancedWishlist'])->name('api.wishlist.enhanced');
