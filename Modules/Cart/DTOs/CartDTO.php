<?php

declare(strict_types=1);

namespace Modules\Cart\DTOs;

use Illuminate\Http\Request;
use Modules\Cart\Http\Requests\Api\Store;

readonly class CartDTO
{
    public function __construct(
        public ?int $id,
        public ?int $product_id,
        public ?int $quantity,
        public ?int $user_id,
        public ?float $price,
        public ?string $session_id = null,
        public ?float $amount = null,
        public ?int $order_id = null
    ) {}

    public static function fromRequest(Store|Request $request): self
    {
        return self::fromArray($request->validated());
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

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->product_id,
            $this->quantity,
            $this->user_id,
            $this->price,
            $this->session_id,
            $this->amount,
            $this->order_id
        );
    }
}
