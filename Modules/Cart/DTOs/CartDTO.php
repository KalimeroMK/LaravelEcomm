<?php

declare(strict_types=1);

namespace Modules\Cart\DTOs;

use Illuminate\Http\Request;
use Modules\Cart\Models\Cart;

readonly class CartDTO
{
    public function __construct(
        public ?int $id,
        public ?int $product_id,
        public ?int $quantity,
        public ?int $user_id,
        public ?float $price,
        public ?string $session_id,
        public ?float $amount,
        public ?int $order_id
    ) {}

    public static function fromRequest(Request $request, ?int $id = null, ?Cart $existing = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            $data['product_id'] ?? $existing?->product_id,
            $data['quantity'] ?? $existing?->quantity,
            $data['user_id'] ?? $existing?->user_id,
            $data['price'] ?? $existing?->price,
            $data['session_id'] ?? $existing?->session_id,
            $data['amount'] ?? $existing?->amount,
            $data['order_id'] ?? $existing?->order_id,
        );
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['product_id'] ?? null,
            $data['quantity'] ?? null,
            $data['user_id'] ?? null,
            $data['price'] ?? null,
            $data['session_id'] ?? null,
            $data['amount'] ?? null,
            $data['order_id'] ?? null
        );
    }
}
