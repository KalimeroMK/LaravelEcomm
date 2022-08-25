<?php

namespace Modules\Billing\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;
use Modules\Core\Helpers\Payment;
use Modules\Core\Http\Controllers\Api\CoreController;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeController extends CoreController
{
    /**
     * success response method.
     *
     * @return void
     * @throws ApiErrorException
     */
    public function stripe(\Modules\Billing\Http\Requests\Api\Stripe $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $data = Payment::calculate($request);
        
        Charge::create([
            "amount"      => $data[1] * 100,
            "currency"    => "usd",
            "source"      => $request->stripeToken,
            "description" => "Test payment from zbogoevski@gmail.com.",
        ]);
        Cart::where('user_id', Auth::id())->where('order_id', null)->update(['order_id' => $data[0]]);
    }
}