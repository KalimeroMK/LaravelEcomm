<?php

namespace Modules\Billing\Service;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Billing\Http\Controllers\StripeController;
use Modules\Cart\Models\Cart;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeService
{
    private StripeController $stripe_controller;
    
    public function __construct(StripeController $stripe_controller)
    {
        $this->stripe_controller = $stripe_controller;
    }
    
    /**
     * success response method.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function stripePost(Request $request): RedirectResponse
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $data = $this->stripe_controller->payment->calculate($request);
        
        Charge::create([
            "amount"      => $data[1] * 100,
            "currency"    => "usd",
            "source"      => $request->stripeToken,
            "description" => "Test payment from zbogoevski@gmail.com.",
        ]);
        Cart::where('user_id', Auth::id())->where('order_id', null)->update(['order_id' => $data[0]]);
        Session::flash('success', 'Payment successful!');
        
        return back();
    }
    
    /**
     * success response method.
     *
     * @param $id
     *
     * @return Application|Factory|View
     */
    public function stripe($id): View|Factory|Application
    {
        return view('front::pages.stripe', compact('id'));
    }
}