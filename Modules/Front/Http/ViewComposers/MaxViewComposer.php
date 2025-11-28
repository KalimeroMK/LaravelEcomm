<?php

declare(strict_types=1);

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Product\Models\Product;

class MaxViewComposer
{
    public function compose(View $view): void
    {
        $max = Product::max('price');
        $view->with('max', $max ?? 1000); // Default to 1000 if no products exist
    }
}
