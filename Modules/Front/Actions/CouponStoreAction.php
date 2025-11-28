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
