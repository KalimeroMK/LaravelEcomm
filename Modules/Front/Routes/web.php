<?php

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
Route::get('/product-lists', [FrontController::class, 'productLists'])->name('front.product-lists');
Route::match(['get', 'post'], '/filter', [FrontController::class, 'productFilter'])->name('front.product-filter');
Route::get('/product-grids', [FrontController::class, 'productGrids'])->name('front.product-grids');
Route::get('/bundles', [FrontController::class, 'bundles'])->name('front.bundles');

// NewsLetter
Route::post('/subscribe', [\Modules\Front\Http\Controllers\Api\FrontController::class, 'subscribe'])->name('subscribe');
Route::get('/validation/{token}', [FrontController::class, 'verifyNewsletter'])->name('validation');
Route::get('/delete/{token}', [FrontController::class, 'deleteNewsletter'])->name('delete-newsletter');

