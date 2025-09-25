<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

use Illuminate\Http\Request;

readonly class ProductReviewDTO
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $user_id,
        public string $review,
        public int $rate
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $data = $request->validated();

        return new self(
            $id,
            (int) $data['product_id'],
            $data['user_id'] ?? auth()->id(),
            $data['review'],
            (int) $data['rate'],
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'review' => $this->review,
            'rate' => $this->rate,
        ];
    }
}
