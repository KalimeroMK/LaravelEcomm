<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Stripe;

use Modules\Billing\DTOs\StripeDTO;
use Stripe\Charge;
use Stripe\Stripe;

readonly class CreateStripeChargeAction
{
    public function execute(StripeDTO $dto): Charge
    {
        Stripe::setApiKey(config('stripe.'.config('stripe.mode', 'sandbox').'.client_secret'));

        return Charge::create([
            'amount' => (int) round($dto->amount * 100),
            'currency' => $dto->currency,
            'source' => $dto->source,
            'description' => $dto->description,
        ]);
    }
}
