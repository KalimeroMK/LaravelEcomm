<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Attribute\Services\LayeredNavigationService;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;

readonly class ProductGridsAction
{
    public function __construct(
        private LayeredNavigationService $layeredNavigationService
    ) {}

    public function __invoke(): array
    {
        $queryParams = request()->only([
            'category', 'brand', 'price', 'show', 'sortBy',
            // Dynamic attribute filters
            'color', 'size', 'material', 'weight', 'length', 'width', 'height',
        ]);

        // Generate main cache key based on all parameters
        $mainCacheKey = 'product_grids_'.md5(json_encode($queryParams));

        return Cache::remember($mainCacheKey, 1800, function () use ($queryParams): array {
            // Retrieve category and brand IDs from slugs with caching
            $categorySlugs = explode(',', $queryParams['category'] ?? '');
            $brandSlugs = explode(',', $queryParams['brand'] ?? '');

            $categoryIds = $this->getCachedCategoryIds($categorySlugs);
            $brandIds = $this->getCachedBrandIds($brandSlugs);

            // Parse price range
            [$minPrice, $maxPrice] = $this->parsePriceRange($queryParams['price'] ?? '');

            // Parse sorting
            [$sortColumn, $sortOrder] = $this->parseSorting($queryParams['sortBy'] ?? 'created_at');

            $perPage = (int) ($queryParams['show'] ?? 9);

            // Build attribute filters
            $attributeFilters = $this->buildAttributeFilters($queryParams);

            // Get products with caching
            $productsCacheKey = 'products_grid_'.md5(json_encode([
                'categoryIds' => $categoryIds,
                'brandIds' => $brandIds,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'sortColumn' => $sortColumn,
                'sortOrder' => $sortOrder,
                'perPage' => $perPage,
                'attributeFilters' => $attributeFilters,
            ]));

            $products = Cache::remember($productsCacheKey, 900, function () use ($categoryIds, $brandIds, $minPrice, $maxPrice, $sortColumn, $sortOrder, $perPage, $attributeFilters) {
                $query = Product::query()
                    ->when($categoryIds, fn ($query) => $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds)))
                    ->when($brandIds, fn ($query) => $query->whereIn('brand_id', $brandIds))
                    ->when($minPrice || $maxPrice, fn ($query) => $query->whereBetween('price', [$minPrice, $maxPrice]))
                    ->where('status', 'active')
                    ->whereNull('parent_id'); // Only show parent/simple products, not variants

                // Apply attribute filters
                if (! empty($attributeFilters)) {
                    $query = $this->layeredNavigationService->applyFilters($query, $attributeFilters);
                }

                return $query->orderBy($sortColumn, $sortOrder)
                    ->with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
                    ->paginate($perPage);
            });

            // Get brands with caching
            $brandsCacheKey = 'active_brands_grid';
            $brands = Cache::remember($brandsCacheKey, 3600, function () {
                return Brand::where('status', 'active')
                    ->orderBy('title')
                    ->get();
            });

            // Get recent products with caching
            $recentProductsCacheKey = 'recent_products_grid_3';
            $recent_products = Cache::remember($recentProductsCacheKey, 1800, function () {
                return Product::where('status', 'active')
                    ->whereNull('parent_id')
                    ->orderByDesc('id')
                    ->with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
                    ->take(3)
                    ->get();
            });

            // Get layered navigation filters
            $layeredFilters = $this->layeredNavigationService->getAvailableFilters(
                $attributeFilters,
                $categoryIds
            );

            // Get active filters for display
            $activeFilters = $this->layeredNavigationService->getActiveFilters($queryParams);

            return [
                'brands' => $brands,
                'recent_products' => $recent_products,
                'products' => $products,
                'layered_filters' => $layeredFilters,
                'active_filters' => $activeFilters,
                'price_range' => ['min' => $minPrice, 'max' => $maxPrice],
            ];
        });
    }

    /**
     * Build attribute filters from query params
     */
    private function buildAttributeFilters(array $params): array
    {
        $filters = [];

        // Known attribute codes (can be extended dynamically)
        $attributeCodes = ['color', 'size', 'material', 'weight', 'length', 'width', 'height', 'brand'];

        foreach ($attributeCodes as $code) {
            if (isset($params[$code]) && ! empty($params[$code])) {
                $values = is_string($params[$code]) ? explode(',', $params[$code]) : $params[$code];
                $filters[$code] = $values;
            }
        }

        return $filters;
    }

    /**
     * Get cached category IDs
     */
    private function getCachedCategoryIds(array $slugs): array
    {
        if ($slugs === [] || $slugs === ['']) {
            return [];
        }

        $cacheKey = 'category_ids_grid_'.md5(json_encode($slugs));

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
        if ($slugs === [] || $slugs === ['']) {
            return [];
        }

        $cacheKey = 'brand_ids_grid_'.md5(json_encode($slugs));

        return Cache::remember($cacheKey, 86400, function () use ($slugs) {
            return Brand::whereIn('slug', $slugs)
                ->where('status', 'active')
                ->pluck('id')
                ->toArray();
        });
    }

    /**
     * Parse price range
     */
    private function parsePriceRange(?string $priceRange): array
    {
        if ($priceRange === null || $priceRange === '' || $priceRange === '0') {
            return [0, PHP_INT_MAX];
        }

        $prices = explode('-', $priceRange);

        return [
            (int) ($prices[0] ?? 0),
            (int) ($prices[1] ?? PHP_INT_MAX),
        ];
    }

    /**
     * Parse sorting parameters
     */
    private function parseSorting(?string $sortBy): array
    {
        $sortColumn = $sortBy ?? 'created_at';
        $sortOrder = 'desc';

        if ($sortColumn === 'title') {
            $sortOrder = 'asc';
        } elseif ($sortColumn === 'price') {
            $sortOrder = 'asc';
        }

        return [$sortColumn, $sortOrder];
    }
}
