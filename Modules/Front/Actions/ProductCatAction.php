<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class ProductCatAction
{
    public function __invoke(string $slug): array|string
    {
        $cacheKey = 'productCat_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug): string|array {
            $category = Category::whereSlug($slug)
                ->with(['children' => function ($query) {
                    $query->where('status', 1)->withCount('products');
                }])
                ->withCount('products')
                ->first();
            
            if (! $category) {
                return 'Category not found';
            }

            // Get child categories (subcategories)
            $childCategories = $category->children;

            // If no children, show products from this category
            if ($childCategories->isEmpty()) {
                $products = $category->products()->where('status', 'active')->paginate(12);
            } else {
                $products = collect(); // Empty collection
            }

            // Get recent products for sidebar
            $recentProducts = Product::where('status', 'active')
                ->orderBy('id', 'desc')
                ->take(4)
                ->get();

            return [
                'category' => $category,
                'childCategories' => $childCategories,
                'products' => $products,
                'recentProducts' => $recentProducts,
            ];
        });
    }
}
