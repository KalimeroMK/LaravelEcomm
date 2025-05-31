<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Illuminate\Support\Collection;
use Modules\Product\Models\ProductReview;

class GetAllProductReviewsAction
{
    public function execute(): Collection
    {
        return ProductReview::all();
    }
}
