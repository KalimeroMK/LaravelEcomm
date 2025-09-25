<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class ProductCatAction
{
    public function __invoke(string $slug): array|string
    {
        $cacheKey = 'productCat_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug): string|array {
            $category = Category::whereSlug($slug)->first();
            if (! $category) {
                return 'Category not found';
            }
            $products = $category->products()->paginate(10);
            $recentProducts = Product::where('status', 'active')
                ->orderBy('id', 'desc')
                ->take(3)
                ->get();
            $brands = Brand::where('status', 'active')
                ->orderBy('title')
                ->get();

            return [
                'category' => $category,
                'products' => $products,
                'recentProducts' => $recentProducts,
                'brands' => $brands,
            ];
        });
    }
}
