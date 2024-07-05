<?php

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

Route::get('/', [FrontController::class, 'index'])->name('api.home');
Route::get('product-detail/{slug}', [FrontController::class, 'productDetail'])->name('api.product-detail');
Route::post('/product/search', [FrontController::class, 'productSearch'])->name('api.product.search');
Route::get('/product-cat/{slug}', [FrontController::class, 'productCat'])->name('api.product-cat');
Route::get('/product-brand/{slug}', [FrontController::class, 'productBrand'])->name('api.product-brand');
Route::get('/blog', [FrontController::class, 'blog'])->name('api.blog');
Route::get('/blog-detail/{slug}', [FrontController::class, 'blogDetail'])->name('api.blog.detail');
Route::get('/blog/search', [FrontController::class, 'blogSearch'])->name('api.blog.search');
Route::get('blog-cat/{slug}', [FrontController::class, 'blogByCategory'])->name('api.blog.category');
Route::get('blog-tag/{slug}', [FrontController::class, 'blogByTag'])->name('api.blog.tag');
Route::get('/product/deal', [FrontController::class, 'productDeal'])->name('api.product.deal');
