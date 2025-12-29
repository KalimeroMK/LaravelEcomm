<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Models\Coupon;

class CouponStoreAction
{
    public function execute(string $code): array
    {
        $coupon = Coupon::whereCode($code)->first();

        if (! $coupon) {
            throw new InvalidArgumentException('Invalid coupon code, Please try again');
        }

        // Check if coupon is expired
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw new InvalidArgumentException('Coupon has expired');
        }

        // Check if coupon is active
        if ($coupon->status !== 'active') {
            throw new InvalidArgumentException('Coupon is not active');
        }

        $total_price = (float) Cart::whereUserId(Auth::id())->where('order_id', null)->sum('price');

        $couponData = [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'value' => $coupon->discount($total_price),
        ];

        session()->put('coupon', $couponData);

        return $couponData;
    }
}
