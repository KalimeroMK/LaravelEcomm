<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Models\Brand;
use Modules\Product\Models\Product;

class ProductDealAction
{
    public function __invoke(): array
    {
        return Cache::remember('productDeal', 24 * 60, function (): array {
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $products = Product::with('categories')
                ->where('d_deal', true)
                ->orderBy('id', 'DESC')
                ->paginate(9);
            $brands = Brand::with('products')->get();

            return [
                'recent_products' => $recent_products,
                'products' => $products,
                'brands' => $brands,
            ];
        });
    }
}
