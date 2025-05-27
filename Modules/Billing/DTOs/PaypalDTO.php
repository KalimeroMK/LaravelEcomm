<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;

readonly class PaypalDTO
{
    public function __construct(
        public ?float $amount,
        public ?string $currency,
        public ?string $returnUrl,
        public ?string $cancelUrl
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->all());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['amount']) ? (float) $data['amount'] : null,
            $data['currency'] ?? config('paypal.currency', 'usd'),
            $data['returnUrl'] ?? route('payment.success'),
            $data['cancelUrl'] ?? route('payment.cancel')
        );
    }
}
