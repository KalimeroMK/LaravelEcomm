<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;
use Modules\Coupon\Models\Coupon;

class CouponStoreAction
{
    public function __invoke(Request $request): RedirectResponse|string
    {
        $coupon = Coupon::whereCode($request->code)->first();
        if (! $coupon) {
            request()->session()->flash('error', 'Invalid coupon code, Please try again');

            return back();
        }
        $total_price = (float) Cart::whereUserId(Auth::id())->where('order_id', null)->sum('price');
        session()->put('coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'value' => $coupon->discount($total_price),
        ]);
        request()->session()->flash('success', 'Coupon successfully applied');

        return redirect()->back();
    }
}
