<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;

readonly class PaypalDTO
{
    public function __construct(
        public float $amount,
        public string $currency,
        public string $returnUrl,
        public string $cancelUrl
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            (float) $request->input('amount'),
            $request->input('currency', config('paypal.currency', 'usd')),
            $request->input('returnUrl', route('payment.success')),
            $request->input('cancelUrl', route('payment.cancel'))
        );
    }
}
