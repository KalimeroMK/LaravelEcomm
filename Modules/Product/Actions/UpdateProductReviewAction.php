<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductReviewDTO;
use Modules\Product\Models\ProductReview;

class UpdateProductReviewAction
{
    public function execute(int $id, ProductReviewDTO $dto): ProductReview
    {
        $review = ProductReview::findOrFail($id);
        $review->update($dto->toArray());

        return $review;
    }
}
