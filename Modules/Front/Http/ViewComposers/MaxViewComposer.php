<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\Database\QueryException;
use Illuminate\View\View;
use Modules\Product\Models\Product;

class MaxViewComposer
{
    public function compose(View $view): void
    {
        try {
            $max = Product::max('price');
            $view->with('max', $max ?? 1000); // Default to 1000 if no products exist
        } catch (QueryException $e) {
            // Database not available or table doesn't exist, use default
            $view->with('max', 1000);
        }
    }
}
