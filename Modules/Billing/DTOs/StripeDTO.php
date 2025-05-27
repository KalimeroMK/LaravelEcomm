<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;

readonly class StripeDTO
{
    public function __construct(
        public ?float $amount,
        public ?string $currency,
        public ?string $source,
        public ?string $description
    ) {}

    public static function fromRequest(Request $request): self
    {
        return self::fromArray($request->all());
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['amount']) ? (int) $data['amount'] : null,
            $data['currency'] ?? 'usd',
            $data['stripeToken'] ?? null,
            $data['description'] ?? 'KalimeroMK E-comm'
        );
    }
}
