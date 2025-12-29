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
use Modules\Billing\Http\Controllers\BillingController;
use Modules\Billing\Http\Controllers\InvoiceController;
use Modules\Billing\Http\Controllers\PaymentController;
use Modules\Billing\Http\Controllers\PaypalController;
use Modules\Billing\Http\Controllers\StripeController;
use Modules\Billing\Http\Controllers\WishlistController;

// theme_view() is loaded via composer autoload files

Route::get('wishlist', function (): Illuminate\Contracts\View\View|Illuminate\Contracts\View\Factory {
    return view(theme_view('pages.wishlist'));
})->name('wishlist');
Route::get('/wishlist/{slug}', [WishlistController::class, 'wishlist'])->name('add-to-wishlist');
Route::get('wishlist-delete/{id}', [WishlistController::class, 'wishlistDelete'])->name('wishlist-delete');
// // Payment
Route::get('payment', [PaypalController::class, 'charge'])->name('payment');
Route::get('cancel', [PaypalController::class, 'cancel'])->name('payment.cancel');
Route::get('payment/success', [PaypalController::class, 'success'])->name('payment.success');
// Stripe
Route::get('stripe/{id}', [StripeController::class, 'stripe'])->name('stripe');
Route::post('stripe', [StripeController::class, 'stripePost'])->name('stripe.post');

// Billing History (User)
Route::middleware(['auth'])->group(function () {
    Route::get('billing/history', [BillingController::class, 'history'])->name('billing.history');
    Route::get('payments/history', [PaymentController::class, 'history'])->name('payments.history');
});

// Invoices (Admin & User)
Route::middleware(['auth'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
});

// Payment Analytics (Admin only)
Route::middleware(['auth'])->group(function () {
    Route::get('admin/payments/analytics', [PaymentController::class, 'analytics'])->name('payments.analytics');
});
