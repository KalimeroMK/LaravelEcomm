<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers\Api;

use Modules\Billing\Http\Requests\Api\Stripe as StripeData;
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
     *
     * @throws ApiErrorException
     */
    public function stripe(StripeData $request): void
    {
        Stripe::setApiKey(config('stripe.sandbox.client_secret'));
        Charge::create([
            'amount' => Payment::calculate($request) * 100,
            'currency' => 'usd',
            'source' => $request->stripeToken,
            'description' => 'KalimeroMK E-comm',
        ]);
    }
}
