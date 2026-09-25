<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\Api\FrontController;
use Modules\Front\Http\Controllers\Api\ProductFilterController;

/*
|--------------------------------------------------------------------------
| API Routes - Front Module
|--------------------------------------------------------------------------
*/

// Product Filtering & Navigation
Route::prefix('products')->group(function () {
    // AJAX Product Filter
    Route::get('/filter', [ProductFilterController::class, 'filter'])
        ->name('front.api.products.filter');

    // Get Available Filters
    Route::get('/filters', [ProductFilterController::class, 'getFilters'])
        ->name('front.api.products.filters');

    // Get Price Range
    Route::get('/price-range', [ProductFilterController::class, 'getPriceRange'])
        ->name('front.api.products.price-range');
});

// Recently Viewed Products API
Route::get('recently-viewed', [FrontController::class, 'recentlyViewed'])
    ->name('front.api.recently-viewed');

// Public storefront API (JSON versions of the web front pages).
// The Api\FrontController methods existed but were never routed.
Route::get('/', [FrontController::class, 'index'])->name('front.api.index');
Route::get('product-detail/{slug}', [FrontController::class, 'productDetail'])->name('front.api.product-detail');
Route::get('product-grids', [FrontController::class, 'productGrids'])->name('front.api.product-grids');
Route::get('product-lists', [FrontController::class, 'productLists'])->name('front.api.product-lists');
Route::post('product/search', [FrontController::class, 'productSearch'])->name('front.api.product-search');
Route::get('product/deal', [FrontController::class, 'productDeal'])->name('front.api.product-deal');
Route::get('product-brand/{slug}', [FrontController::class, 'productBrand'])->name('front.api.product-brand');
Route::get('product-cat/{slug}', [FrontController::class, 'productCat'])->name('front.api.product-cat');
Route::get('blog', [FrontController::class, 'blog'])->name('front.api.blog');
Route::get('blog-detail/{slug}', [FrontController::class, 'blogDetail'])->name('front.api.blog-detail');
Route::get('blog/search', [FrontController::class, 'blogSearch'])->name('front.api.blog-search');
Route::get('blog-cat/{slug}', [FrontController::class, 'blogByCategory'])->name('front.api.blog-cat');
Route::get('blog-tag/{slug}', [FrontController::class, 'blogByTag'])->name('front.api.blog-tag');
Route::get('bundles', [FrontController::class, 'bundles'])->name('front.api.bundles');
Route::get('bundle-detail/{slug}', [FrontController::class, 'bundleDetail'])->name('front.api.bundle-detail');
Route::get('banners', [FrontController::class, 'banners'])->name('front.api.banners');
