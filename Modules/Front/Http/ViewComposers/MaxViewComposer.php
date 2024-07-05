<?php

namespace Modules\Front\Http\ViewComposers;

use Illuminate\View\View;
use Modules\Product\Models\Product;

class MaxViewComposer
{
    public function compose(View $view): void
    {
        $view->with('max', Product::max('price'));
    }
}
