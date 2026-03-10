<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Carbon;
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

        // Check if coupon is expired (expires_at may be string when not cast)
        $expiresAt = $coupon->expires_at;
        if ($expiresAt !== null && Carbon::parse($expiresAt)->isPast()) {
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
            'value' => $coupon->calculateDiscount($total_price),
        ];

        session()->put('coupon', $couponData);

        return $couponData;
    }
}
