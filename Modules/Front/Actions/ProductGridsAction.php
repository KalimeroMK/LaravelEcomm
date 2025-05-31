<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class ProductGridsAction
{
    public function __invoke(): array
    {
        $queryParams = request()->only(['category', 'brand', 'price', 'show', 'sortBy']);

        // Retrieve category and brand IDs from slugs
        $categorySlugs = explode(',', $queryParams['category'] ?? '');
        $brandSlugs = explode(',', $queryParams['brand'] ?? '');

        $categoryCacheKey = 'category_ids_'.json_encode($categorySlugs);
        $brandCacheKey = 'brand_ids_'.json_encode($brandSlugs);

        $categoryIds = Cache::remember($categoryCacheKey, 86400, function () use ($categorySlugs) {
            return Category::whereIn('slug', $categorySlugs)->pluck('id')->toArray();
        });
        $brandIds = Cache::remember($brandCacheKey, 86400, function () use ($brandSlugs) {
            return Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
        });

        [$minPrice, $maxPrice] = array_map('intval', explode('-', $queryParams['price'] ?? '0-'.PHP_INT_MAX));

        $sortColumn = $queryParams['sortBy'] ?? 'created_at';
        $sortOrder = ($sortColumn === 'title') ? 'asc' : 'desc';
        if ($sortColumn === 'price') {
            $sortOrder = 'asc';
        }

        $perPage = (int) ($queryParams['show'] ?? 9);
        $cacheKey = 'products_'.json_encode([
            'categoryIds' => $categoryIds,
            'brandIds' => $brandIds,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
            'perPage' => $perPage,
        ]);

        $products = Cache::remember($cacheKey, 86400,
            function () use ($categoryIds, $brandIds, $minPrice, $maxPrice, $sortColumn, $sortOrder, $perPage) {
                return Product::query()
                    ->when($categoryIds, fn ($query) => $query->whereIn('cat_id', $categoryIds))
                    ->when($brandIds, fn ($query) => $query->whereIn('brand_id', $brandIds))
                    ->when($minPrice || $maxPrice, fn ($query) => $query->whereBetween('price', [$minPrice, $maxPrice]))
                    ->orderBy($sortColumn, $sortOrder)
                    ->with(['categories', 'brand', 'tags'])
                    ->paginate($perPage);
            });

        $brands = Brand::where('status', 'active')->orderBy('title')->get();
        $recent_products = Product::where('status', 'active')->orderByDesc('id')->take(3)->get();

        return [
            'brands' => $brands,
            'recent_products' => $recent_products,
            'products' => $products,
        ];
    }
}
