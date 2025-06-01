<?php

declare(strict_types=1);

namespace Modules\Shipping\DTOs;

use Illuminate\Http\Request;
use Modules\Shipping\Models\Shipping;

readonly class ShippingDTO
{
    public function __construct(
        public ?int $id,
        public ?string $type,
        public ?float $price,
        public ?string $status,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            type: $data['type'] ?? null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            status: $data['status'] ?? null,
            created_at: isset($data['created_at']) ? (string) $data['created_at'] : null,
            updated_at: isset($data['updated_at']) ? (string) $data['updated_at'] : null,
        );
    }

    public static function fromRequest(Request $request, ?int $id = null, ?Shipping $shipping = null): self
    {
        $data = $request->validated();

        return new self(
            id: $id ?? $data['id'] ?? $shipping?->id,
            type: $data['type'] ?? $shipping?->type,
            price: isset($data['price']) ? (float) $data['price'] : $shipping?->price,
            status: $data['status'] ?? $shipping?->status,
            created_at: $shipping?->created_at?->toDateTimeString(),
            updated_at: $shipping?->updated_at?->toDateTimeString(),
        );
    }
}
