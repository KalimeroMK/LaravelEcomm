<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Brand\Models\Brand;
use Modules\Product\Models\Product;

class ProductSearchAction
{
    public function __invoke(array $data): array
    {
        $searchTerm = Arr::get($data, 'search', '');
        $perPage = Arr::get($data, 'per_page', 9);

        // Generate cache key based on search term and pagination
        $cacheKey = 'product_search_'.md5($searchTerm.'_'.$perPage);

        return Cache::remember($cacheKey, 1800, function () use ($searchTerm, $perPage): array {
            // Get recent products with caching
            $recentProductsCacheKey = 'recent_products_search_'.md5('active_3');
            $recent_products = Cache::remember($recentProductsCacheKey, 1800, function () {
                return Product::whereStatus('active')
                    ->orderBy('id', 'DESC')
                    ->with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
                    ->limit(3)
                    ->get();
            });

            // Search products with caching
            $productsCacheKey = 'search_products_'.md5($searchTerm.'_'.$perPage);
            $products = Cache::remember($productsCacheKey, 900, function () use ($searchTerm, $perPage) {
                return Product::search($searchTerm)
                    ->where('status', 'active')
                    ->orderBy('id', 'desc')
                    ->with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
                    ->paginate($perPage);
            });

            // Search brands with caching
            $brandsCacheKey = 'search_brands_'.md5($searchTerm);
            $brands = Cache::remember($brandsCacheKey, 1800, function () use ($searchTerm) {
                return Brand::search($searchTerm)
                    ->where('status', 'active')
                    ->orderBy('title')
                    ->get();
            });

            return [
                'recent_products' => $recent_products,
                'products' => $products,
                'brands' => $brands,
            ];
        });
    }
}
