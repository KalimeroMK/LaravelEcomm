<?php

declare(strict_types=1);

namespace Modules\Cart\DTOs;

use Illuminate\Http\Request;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;

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

        // Get product_id from slug if slug is provided
        $product_id = $data['product_id'] ?? null;
        if (isset($data['slug']) && ! $product_id) {
            $product = Product::where('slug', $data['slug'])->first();
            $product_id = $product?->id;
        }

        // Auto-calculate price and amount from product if not provided
        $price = $data['price'] ?? $existing?->price;
        $quantity = $data['quantity'] ?? $existing?->quantity ?? 1;
        $amount = $data['amount'] ?? $existing?->amount;

        if (! $price && $product_id) {
            $product = Product::find($product_id);
            $price = $product?->price;
        }

        if (! $amount && $price && $quantity) {
            $amount = $price * $quantity;
        }

        return new self(
            $id,
            $product_id ?? $existing?->product_id,
            $quantity,
            $data['user_id'] ?? $existing?->user_id ?? auth()->id(),
            $price,
            $data['session_id'] ?? $existing?->session_id ?? session()->getId(),
            $amount,
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
