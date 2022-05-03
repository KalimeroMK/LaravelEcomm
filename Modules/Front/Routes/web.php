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

    Route::get('/', [FrontController::class, 'index'])->name('home');
    Route::get('/about-us', [FrontController::class, 'aboutUs'])->name('about-us');
    Route::get('/contact', [FrontController::class, 'contact'])->name('contact');
    Route::post('/contact/message', [FrontController::class, 'messageStore'])->name('contact.store');
    Route::get('product-detail/{slug}', [FrontController::class, 'productDetail'])->name('product-detail');
    Route::post('/product/search', [FrontController::class, 'productSearch'])->name('product.search');
    Route::get('/product-cat/{slug}', [FrontController::class, 'productCat'])->name('product-cat');
    Route::get('/product-brand/{slug}', [FrontController::class, 'productBrand'])->name('product-brand');
    Route::get('/blog', [FrontController::class, 'blog'])->name('blog');
    Route::get('/blog-detail/{slug}', [FrontController::class, 'blogDetail'])->name('blog.detail');
    Route::get('/blog/search', [FrontController::class, 'blogSearch'])->name('blog.search');
    Route::post('/blog/filter', [FrontController::class, 'blogFilter'])->name('blog.filter');
    Route::get('blog-cat/{slug}', [FrontController::class, 'blogByCategory'])->name('blog.category');
    Route::get('blog-tag/{slug}', [FrontController::class, 'blogByTag'])->name('blog.tag');
