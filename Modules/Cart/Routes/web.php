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
use Modules\Cart\Http\Controllers\CartController;

// theme_view() is loaded via composer autoload files

// Cart section
Route::get('/add-to-cart/{slug}', [CartController::class, 'addToCart'])->name('add-to-cart');
Route::post('/add-to-cart', [CartController::class, 'singleAddToCart'])->name('single-add-to-cart');
Route::group(['middleware' => 'auth'], function (): void {
    Route::get('cart-delete/{id}', [CartController::class, 'cartDelete'])->name('cart-delete');
    Route::post('cart-update', [CartController::class, 'cartUpdate'])->name('cart-update');
    Route::get('/cart-list', function (): Illuminate\Contracts\View\Factory|Illuminate\Contracts\View\View {
        return view(theme_view('pages.cart'));
    })->name('cart-list');
    Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout');
});
