<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Web Routes - Front Module
|--------------------------------------------------------------------------
|
| All frontend routes with locale prefix. Routes are wrapped in
| LocaleMiddleware to handle language switching.
|
*/

use App\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\Route;
use Modules\Front\Http\Controllers\FrontController;
use Modules\Front\Http\Controllers\PlaceholderImageController;

// Non-localized routes (no locale prefix needed)
Route::get('placeholder/image', PlaceholderImageController::class)->name('front.placeholder.image');

// Localized routes with locale prefix: /en/, /mk/, /de/
Route::group([
    'prefix' => '{locale}',
    'where' => ['locale' => '[a-zA-Z]{2}'],
    'middleware' => [LocaleMiddleware::class],
], function (): void {
    
    // Home
    Route::get('/', [FrontController::class, 'index'])->name('front.index');
    
    // Static Pages
    Route::get('/about-us', [FrontController::class, 'aboutUs'])->name('front.about-us');
    Route::get('/contact', [FrontController::class, 'contact'])->name('front.contact');
    Route::post('/contact/message', [FrontController::class, 'messageStore'])->name('front.store-message');
    
    // Product Routes
    Route::get('/products', [FrontController::class, 'products'])->name('front.products');
    Route::get('/products/{slug}', [FrontController::class, 'productDetail'])->name('front.product.detail');
    Route::get('/product-detail/{slug}', [FrontController::class, 'productDetail'])->name('front.product-detail'); // Alias for compatibility
    Route::post('/product/search', [FrontController::class, 'productSearch'])->name('front.product-search');
    Route::get('/product/deal', [FrontController::class, 'productDeal'])->name('front.product-deal');
    Route::get('/product-lists', [FrontController::class, 'productLists'])->name('front.product-lists');
    Route::get('/product-grids', [FrontController::class, 'productGrids'])->name('front.product-grids');
    Route::match(['get', 'post'], '/filter', [FrontController::class, 'productFilter'])->name('front.product-filter');
    
    // Category Routes
    Route::get('/categories', [FrontController::class, 'categories'])->name('front.categories');
    Route::get('/categories/{slug}', [FrontController::class, 'categoryDetail'])->name('front.category.detail');
    Route::get('/product-cat/{slug}', [FrontController::class, 'productCat'])->name('front.product-cat');
    Route::get('/product-brand/{slug}', [FrontController::class, 'productBrand'])->name('front.product-brand');
    
    // Blog Routes
    Route::get('/blog', [FrontController::class, 'blog'])->name('front.blog');
    Route::get('/blog/{slug}', [FrontController::class, 'postDetail'])->name('front.post.detail');
    Route::get('/blog-detail/{slug}', [FrontController::class, 'blogDetail'])->name('front.blog-detail'); // Alias
    Route::get('/blog/search', [FrontController::class, 'blogSearch'])->name('front.blog-search');
    Route::post('/blog/filter', [FrontController::class, 'blogFilter'])->name('front.blog-filter');
    Route::get('/blog-cat/{slug}', [FrontController::class, 'blogByCategory'])->name('front.blog-by-category');
    Route::get('/blog-tag/{slug}', [FrontController::class, 'blogByTag'])->name('front.blog-by-tag');
    
    // Bundle Routes
    Route::get('/bundle', [FrontController::class, 'bundles'])->name('front.bundles');
    Route::get('/bundle/{slug}', [FrontController::class, 'bundleDetail'])->name('front.bundle-detail');
    
    // Page Routes
    Route::get('/pages/{slug}', [FrontController::class, 'page'])->name('front.page');
    Route::get('/page/{slug}', [FrontController::class, 'pages'])->name('front.pages'); // Alias
    
    // Banner Routes
    Route::get('/banners', [FrontController::class, 'banners'])->name('front.banners');
    Route::post('/banner/impression/{id}', [FrontController::class, 'bannerImpression'])->name('banner.impression');
    
    // Cart Routes (Public)
    Route::get('/cart', [FrontController::class, 'cart'])->name('front.cart');
    Route::post('/cart/add', [FrontController::class, 'cartAdd'])->name('front.cart.add');
    Route::post('/cart/order', [FrontController::class, 'store'])->name('front.cart.order');
    
    // Recently Viewed
    Route::get('/recently-viewed', [FrontController::class, 'recentlyViewed'])->name('front.recently-viewed');
    
    // Advanced Search & Recommendations
    Route::get('/advanced-search', [FrontController::class, 'advancedSearch'])->name('front.advanced-search');
    Route::get('/search-suggestions', [FrontController::class, 'searchSuggestions'])->name('front.search-suggestions');
    Route::get('/recommendations', [FrontController::class, 'recommendations'])->name('front.recommendations');
    Route::get('/related-products/{slug}', [FrontController::class, 'relatedProducts'])->name('front.related-products');
    Route::get('/enhanced-wishlist', [FrontController::class, 'enhancedWishlist'])->name('front.enhanced-wishlist');
    
    // Newsletter
    Route::post('/subscribe', [Modules\Front\Http\Controllers\Api\FrontController::class, 'subscribe'])->name('subscribe');
    Route::get('/validation/{token}', [FrontController::class, 'verifyNewsletter'])->name('validation');
    Route::get('/delete/{token}', [FrontController::class, 'deleteNewsletter'])->name('delete-newsletter');
    
    // Auth Protected Routes
    Route::middleware(['auth'])->group(function () {
        // Checkout Process
        Route::post('/checkout', [FrontController::class, 'checkoutProcess'])->name('front.checkout.process');
        
        // User Orders
        Route::get('/my-orders', [FrontController::class, 'myOrders'])->name('front.my-orders');
        Route::get('/my-orders/{order}', [FrontController::class, 'orderDetail'])->name('front.order-detail');
        Route::post('/my-orders/{order}/reorder', [FrontController::class, 'reorder'])->name('front.reorder');
    });
    
    // Note: cart-list, checkout, cart-delete, cart-update are defined in Modules/Cart/Routes/web.php
    // TODO: Add LocaleMiddleware to Cart module routes for consistency
});
