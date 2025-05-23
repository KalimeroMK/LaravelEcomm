<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Modules\Bundle\Models\Bundle;
use Modules\Product\Models\Product;

class ProductBundlesAction
{
    public function __invoke(): array
    {
        $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
        $products = Bundle::query()->paginate(request('show', 6));

        return [
            'recent_products' => $recent_products,
            'products' => $products,
        ];
    }
}
