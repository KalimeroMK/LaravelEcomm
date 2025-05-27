<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

use Illuminate\Http\Request;
use Modules\Product\Models\ProductReview;

readonly class ProductReviewDTO
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $user_id,
        public int $rating,
        public string $review,
        public ?string $created_at = null,
        public ?string $updated_at = null,
    ) {}

    public static function fromRequest(Request $request, ?int $id = null): self
    {
        $data = $request->validated();
        return new self(
            $id,
            $data['product_id'],
            $data['user_id'] ?? auth()->id(),
            $data['rating'],
            $data['review'],
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }

    public static function fromModel(ProductReview $review): self
    {
        return new self(
            $review->id,
            $review->product_id,
            $review->user_id,
            $review->rating,
            $review->review,
            $review->created_at,
            $review->updated_at,
        );
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->product_id,
            'user_id' => $this->user_id,
            'rating' => $this->rating,
            'review' => $this->review,
        ];
    }
}
