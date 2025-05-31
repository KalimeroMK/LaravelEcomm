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
            $data['id'] ?? null,
            $data['type'] ?? null,
            $data['price'] ?? null,
            $data['status'] ?? null,
            isset($data['created_at']) ? (string) $data['created_at'] : null,
            isset($data['updated_at']) ? (string) $data['updated_at'] : null,
        );
    }

    public static function fromRequest(Request $request, ?int $id = null, ?Shipping $shipping = null): self
    {
        $validated = $request->validated();

        return new self(
            $id ?? $validated['id'] ?? $shipping?->id,
            $validated['type'] ?? $shipping?->type,
            $validated['price'] ?? $shipping?->price,
            $validated['status'] ?? $shipping?->status,
            $shipping?->created_at?->toDateTimeString(),
            $shipping?->updated_at?->toDateTimeString(),
        );
    }
}
