<?php

namespace Modules\Billing\Service;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Modules\Billing\Http\Traits\Order;
use Modules\Core\Helpers\Payment;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeService
{
    use Order;

    private Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function stripePost(Request $request): RedirectResponse
    {
        Stripe::setApiKey(config('stripe.sandbox.client_secret'));
        Charge::create([
            'amount' => $this->payment->calculate($request) * 100,
            'currency' => 'usd',
            'source' => $request->stripeToken,
            'description' => 'KalimeroMK E-comm',
        ]);
        $this->orderSave($this->payment->calculate($request));
        Session::flash('success', 'Payment successful!');

        return redirect()->route('order.index');
    }

    /**
     * success response method.
     */
    public function stripe(int $id): View|Factory|Application
    {
        return view('front::pages.stripe', compact('id'));
    }
}
