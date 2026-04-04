<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Brand\Repository\BrandRepository;
use Modules\Category\Repository\CategoryRepository;
use Modules\Product\Models\Product;
use Modules\Product\Services\ElasticsearchService;

class GetAdvancedSearchAction
{
    public function __construct(
        private readonly ElasticsearchService $elasticsearchService,
        private readonly CategoryRepository $categoryRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(string $query, array $filters): array
    {
        $products       = collect();
        $totalResults   = 0;
        $searchPerformed = false;

        if ($query) {
            $searchPerformed = true;
            $products        = $this->elasticsearchService->search($query, $filters);

            if ($products === null) {
                Log::warning('Elasticsearch unavailable, falling back to SQL search');
                $products = $this->elasticsearchService->searchFallback($query, $filters);
            }

            $totalResults = $products?->count() ?? 0;
        }

        $availableFilters = $this->getAvailableFilters($query);

        return [
            'products'         => $products,
            'query'            => $query,
            'filters'          => $filters,
            'availableFilters' => $availableFilters,
            'totalResults'     => $totalResults,
            'searchPerformed'  => $searchPerformed,
        ];
    }

    /**
     * Build available filter options for the search UI.
     * Cached per query term — invalidated with the product generation counter.
     */
    private function getAvailableFilters(?string $query): array
    {
        $cacheKey = 'search_filters_'.md5((string) $query);

        return Cache::remember($cacheKey, 3600, function () use ($query) {
            $baseQuery = Product::where('status', 'active');

            if ($query) {
                $baseQuery->where(fn ($q) => $q
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                );
            }

            $priceRange = (clone $baseQuery)
                ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
                ->first();

            $brands = (clone $baseQuery)
                ->join('brands', 'products.brand_id', '=', 'brands.id')
                ->select('brands.id', 'brands.name')
                ->distinct()
                ->get();

            $categories = (clone $baseQuery)
                ->join('category_product', 'products.id', '=', 'category_product.product_id')
                ->join('categories', 'category_product.category_id', '=', 'categories.id')
                ->select('categories.id', 'categories.name')
                ->distinct()
                ->get();

            return [
                'price_range'  => ['min' => $priceRange->min_price ?? 0, 'max' => $priceRange->max_price ?? 1000],
                'brands'       => $brands,
                'categories'   => $categories,
                'statuses'     => ['active', 'inactive'],
                'stock_options' => ['in_stock', 'out_of_stock'],
            ];
        });
    }
}
