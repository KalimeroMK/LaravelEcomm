<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Product;

class ProductDetailAction
{
    public function __invoke(string $slug): array
    {
        $cacheKey = 'productDetail_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
            $product_detail = Product::getProductBySlug($slug);

            $related = Product::with('categories')
                ->whereHas('categories', function ($q) use ($product_detail) {
                    $q->whereIn('title', $product_detail->categories->pluck('title'));
                })
                ->where('id', '!=', $product_detail->id)
                ->limit(8)
                ->get();

            return [
                'product_detail' => $product_detail,
                'related' => $related,
            ];
        });
    }
}
