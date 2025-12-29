<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\Models\ProductReview;

readonly class FindProductReviewAction
{
    public function execute(int $id): ProductReview
    {
        return ProductReview::with(['user', 'product'])->findOrFail($id);
    }
}
