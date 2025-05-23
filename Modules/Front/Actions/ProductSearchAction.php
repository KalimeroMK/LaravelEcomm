<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Arr;
use Modules\Brand\Models\Brand;
use Modules\Product\Models\Product;

class ProductSearchAction
{
    public function __invoke(array $data): array
    {
        $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
        $searchTerm = Arr::get($data, 'search', '');
        $products = Product::search($searchTerm)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->paginate(9);
        $brands = Brand::search($searchTerm)
            ->where('status', 'active')
            ->get();

        return [
            'recent_products' => $recent_products,
            'products' => $products,
            'brands' => $brands,
        ];
    }
}
