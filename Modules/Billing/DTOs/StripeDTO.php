<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;

readonly class StripeDTO
{
    public function __construct(
        public int $amount,
        public string $currency,
        public string $source,
        public string $description
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            (int) $request->input('amount'),
            $request->input('currency', 'usd'),
            $request->input('stripeToken'),
            $request->input('description', 'KalimeroMK E-comm')
        );
    }
}
