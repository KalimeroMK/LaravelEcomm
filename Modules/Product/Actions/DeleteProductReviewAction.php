<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\Models\ProductReview;

class DeleteProductReviewAction
{
    public function execute(int $id): void
    {
        ProductReview::findOrFail($id)->delete();
    }
}
