<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductReviewDTO;
use Modules\Product\Models\ProductReview;

class StoreProductReviewAction
{
    public function execute(ProductReviewDTO $dto): ProductReview
    {
        return ProductReview::create($dto->toArray());
    }
}
