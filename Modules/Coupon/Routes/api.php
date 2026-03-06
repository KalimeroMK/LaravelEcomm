<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Coupon\Http\Controllers\Api\CouponController;
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

// Public coupon routes
Route::prefix('coupons')->group(function (): void {
    // Apply coupon to cart
    Route::post('apply', [FrontController::class, 'couponStore'])
        ->name('api.coupons.apply');
    
    // Remove coupon from cart
    Route::delete('remove', [FrontController::class, 'couponRemove'])
        ->name('api.coupons.remove');
    
    // Get currently applied coupon
    Route::get('applied', function (): \Illuminate\Http\JsonResponse {
        $coupon = session()->get('coupon');
        return response()->json([
            'coupon' => $coupon,
            'has_coupon' => !is_null($coupon),
        ]);
    })->name('api.coupons.applied');
    
    // Validate coupon (check if valid without applying)
    Route::post('validate', [CouponController::class, 'validateCoupon'])
        ->name('api.coupons.validate');
});

// Admin coupon management routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function (): void {
    Route::apiResource('coupons', CouponController::class)
        ->names('api.coupons');
    
    // Get coupon usage statistics
    Route::get('coupons/{id}/usage', [CouponController::class, 'usage'])
        ->name('api.coupons.usage');
});
