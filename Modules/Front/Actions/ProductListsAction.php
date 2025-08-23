<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

class ProductListsAction
{
    public function __invoke(): array
    {
        $queryParams = request()->only(['category', 'brand', 'sortBy', 'price', 'show']);
        
        // Generate cache key based on all parameters
        $cacheKey = 'product_lists_' . md5(json_encode($queryParams));
        
        return Cache::remember($cacheKey, 3600, function () use ($queryParams) {
            $query = Product::query()
                ->with(['categories', 'brand', 'tags'])
                ->where('status', 'active');

            // Filter by category with caching
            if (!empty($queryParams['category'])) {
                $catSlugs = explode(',', $queryParams['category']);
                $catIds = $this->getCachedCategoryIds($catSlugs);
                $query->whereIn('cat_id', $catIds);
            }

            // Filter by brand with caching
            if (!empty($queryParams['brand'])) {
                $brandSlugs = explode(',', $queryParams['brand']);
                $brandIds = $this->getCachedBrandIds($brandSlugs);
                $query->whereIn('brand_id', $brandIds);
            }

            // Sort by
            if (!empty($queryParams['sortBy'])) {
                $sortColumn = $queryParams['sortBy'];
                $sortOrder = ($sortColumn === 'title') ? 'asc' : 'desc';
                if ($sortColumn === 'price') {
                    $sortOrder = 'asc';
                }
                $query->orderBy($sortColumn, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Filter by price range
            if (!empty($queryParams['price'])) {
                $priceRange = explode('-', $queryParams['price']);
                $minPrice = (float) ($priceRange[0] ?? 0);
                $maxPrice = (float) ($priceRange[1] ?? PHP_INT_MAX);
                $query->whereBetween('price', [$minPrice, $maxPrice]);
            }

            $perPage = isset($queryParams['show']) ? (int) $queryParams['show'] : 6;
            
            // Get products with pagination
            $products = $query->paginate($perPage);

            // Get recent products with caching
            $recentProductsCacheKey = 'recent_products_' . md5('active_3');
            $recent_products = Cache::remember($recentProductsCacheKey, 1800, function () {
                return Product::where('status', 'active')
                    ->orderBy('id', 'DESC')
                    ->limit(3)
                    ->get();
            });

            // Get brands with caching
            $brandsCacheKey = 'active_brands_with_count';
            $brands = Cache::remember($brandsCacheKey, 3600, function () {
                return Brand::where('status', 'active')
                    ->withCount('products')
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

    /**
     * Get cached category IDs
     */
    private function getCachedCategoryIds(array $slugs): array
    {
        $cacheKey = 'category_ids_' . md5(json_encode($slugs));
        
        return Cache::remember($cacheKey, 86400, function () use ($slugs) {
            return Category::whereIn('slug', $slugs)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
        });
    }

    /**
     * Get cached brand IDs
     */
    private function getCachedBrandIds(array $slugs): array
    {
        $cacheKey = 'brand_ids_' . md5(json_encode($slugs));
        
        return Cache::remember($cacheKey, 86400, function () use ($slugs) {
            return Brand::whereIn('slug', $slugs)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
        });
    }
}
