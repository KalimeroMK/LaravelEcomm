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
use Modules\Billing\Http\Controllers\PaymentProviderController;
use Modules\Billing\Http\Controllers\PaypalController;
use Modules\Billing\Http\Controllers\StripeController;
use Modules\Billing\Http\Controllers\WishlistController;

Route::get('wishlist', function () {
    return view('front::pages.wishlist');
})->name('wishlist');
Route::get('/wishlist/{slug}', [WishlistController::class, 'wishlist'])->name('add-to-wishlist');
Route::get('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlist-delete');
//// Payment
Route::get('payment', [PaypalController::class, 'charge'])->name('payment');
Route::get('cancel', [PaypalController::class, 'cancel'])->name('payment.cancel');
Route::get('payment/success', [PaypalController::class, 'success'])->name('payment.success');
//Stripe
Route::get('stripe/{id}', [StripeController::class, 'stripe'])->name('stripe');
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');
Route::middleware(['auth'])->prefix('admin')->group(function (): void {
    Route::resource('payment_provider', PaymentProviderController::class)->only(
        'index',
        'edit',
        'update'
    );
});
