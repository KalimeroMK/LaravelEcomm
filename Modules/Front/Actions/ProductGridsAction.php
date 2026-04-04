<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Attribute\Services\LayeredNavigationService;
use Modules\Brand\Repository\BrandRepository;
use Modules\Category\Repository\CategoryRepository;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

readonly class ProductGridsAction
{
    public function __construct(
        private ProductRepository $productRepository,
        private CategoryRepository $categoryRepository,
        private BrandRepository $brandRepository,
        private LayeredNavigationService $layeredNavigationService,
    ) {}

    public function __invoke(): array
    {
        $queryParams = request()->only([
            'category', 'brand', 'price', 'show', 'sortBy',
            'color', 'size', 'material', 'weight', 'length', 'width', 'height',
        ]);

        $cacheKey = 'product_grids_'.md5(json_encode($queryParams));

        return Cache::remember($cacheKey, 1800, function () use ($queryParams): array {
            $categoryIds     = $this->categoryRepository->getIdsBySlugs(explode(',', $queryParams['category'] ?? ''));
            $brandIds        = $this->brandRepository->getIdsBySlugs(explode(',', $queryParams['brand'] ?? ''));
            [$minPrice, $maxPrice] = $this->parsePriceRange($queryParams['price'] ?? '');
            [$sortColumn, $sortOrder] = $this->parseSorting($queryParams['sortBy'] ?? 'created_at');
            $perPage         = (int) ($queryParams['show'] ?? 9);
            $attributeFilters = $this->buildAttributeFilters($queryParams);

            $products = Product::query()
                ->when($categoryIds, fn ($q) => $q->whereHas('categories', fn ($sq) => $sq->whereIn('categories.id', $categoryIds)))
                ->when($brandIds, fn ($q) => $q->whereIn('brand_id', $brandIds))
                ->when($minPrice || $maxPrice < PHP_INT_MAX, fn ($q) => $q->whereBetween('price', [$minPrice, $maxPrice]))
                ->where('status', 'active')
                ->whereNull('parent_id')
                ->when(! empty($attributeFilters), fn ($q) => $this->layeredNavigationService->applyFilters($q, $attributeFilters))
                ->orderBy($sortColumn, $sortOrder)
                ->with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
                ->paginate($perPage);

            $brands     = Cache::remember('active_brands_list', 3600, fn () => $this->brandRepository->getActive());
            $categories = Cache::remember('active_categories_grid', 3600, fn () => $this->categoryRepository->getActive());
            $recent_products = Cache::remember('recent_products_sidebar', 1800, fn () => $this->productRepository->getRecent(3));
            $max        = Cache::remember('max_product_price', 3600, fn () => $this->productRepository->getMaxPrice());

            $layeredFilters = $this->layeredNavigationService->getAvailableFilters($attributeFilters, $categoryIds);
            $activeFilters  = $this->layeredNavigationService->getActiveFilters($queryParams);

            return [
                'brands'          => $brands,
                'categories'      => $categories,
                'recent_products' => $recent_products,
                'products'        => $products,
                'layered_filters' => $layeredFilters,
                'active_filters'  => $activeFilters,
                'price_range'     => ['min' => $minPrice, 'max' => $maxPrice],
                'max'             => $max,
            ];
        });
    }

    private function buildAttributeFilters(array $params): array
    {
        $filters = [];
        foreach (['color', 'size', 'material', 'weight', 'length', 'width', 'height', 'brand'] as $code) {
            if (! empty($params[$code])) {
                $filters[$code] = is_string($params[$code]) ? explode(',', $params[$code]) : $params[$code];
            }
        }

        return $filters;
    }

    private function parsePriceRange(?string $priceRange): array
    {
        if (! $priceRange) {
            return [0, PHP_INT_MAX];
        }
        $parts = explode('-', $priceRange);

        return [(int) ($parts[0] ?? 0), (int) ($parts[1] ?? PHP_INT_MAX)];
    }

    private function parseSorting(?string $sortBy): array
    {
        $col   = $sortBy ?? 'created_at';
        $order = in_array($col, ['title', 'price']) ? 'asc' : 'desc';

        return [$col, $order];
    }
}
