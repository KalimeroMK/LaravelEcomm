<?php

namespace Modules\Billing\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;
use Modules\Core\Helpers\Payment;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * success response method.
     *
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function stripe(Request $request)
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