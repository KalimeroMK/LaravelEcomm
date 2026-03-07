<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Stripe;

use Modules\Billing\DTOs\StripeDTO;
use Stripe\Charge;
use Stripe\Stripe;

readonly class CreateStripeChargeAction
{
    public function execute(StripeDTO $dto): void
    {
        Stripe::setApiKey(env('STRIPE_SECRET')); // Use env directly for reliability
        Charge::create([
            'amount' => $dto->amount * 100,
            'currency' => $dto->currency,
            'source' => $dto->source,
            'description' => $dto->description,
        ]);
    }
}
