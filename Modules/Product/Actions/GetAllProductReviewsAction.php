<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\Models\ProductReview;

class GetAllProductReviewsAction
{
    public function execute(): \Illuminate\Support\Collection
    {
        return ProductReview::all();
    }
}
