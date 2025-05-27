<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Product\Models\ProductReview;

class GetProductReviewsByUserAction
{
    public function execute(): \Illuminate\Support\Collection
    {
        return ProductReview::where('user_id', Auth::id())->get();
    }
}
