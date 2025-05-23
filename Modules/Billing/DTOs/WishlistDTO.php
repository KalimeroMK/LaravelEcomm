<?php

declare(strict_types=1);

namespace Modules\Billing\DTOs;

use Illuminate\Http\Request;

readonly class WishlistDTO
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $user_id,
        public int $quantity,
        public float $price,
        public float $discount
    ) {}

    public static function fromRequest(Request $request): self
    {
        $data = $request->all();

        return new self(
            $data['id'] ?? null,
            $data['product_id'],
            $data['user_id'],
            $data['quantity'] ?? 1,
            $data['price'],
            $data['discount'] ?? 0.0
        );
    }
}
