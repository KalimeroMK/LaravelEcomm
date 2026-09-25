<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Modules\Product\Models\Product;

class MaxViewComposer
{
    public function compose(View $view): void
    {
        try {
            $max = Cache::remember('max_product_price', 3600, function (): float {
                return (float) (Product::where('status', 'active')->max('price') ?? 1000);
            });
            $view->with('max', $max);
        } catch (QueryException $e) {
            // Database not available or table doesn't exist, use default
            $view->with('max', 1000);
        }
    }
}
