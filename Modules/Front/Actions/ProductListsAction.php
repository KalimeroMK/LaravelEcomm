<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Repository\BrandRepository;
use Modules\Category\Repository\CategoryRepository;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

class ProductListsAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(): array
    {
        $queryParams = request()->only(['category', 'brand', 'sortBy', 'price', 'show']);
        $cacheKey    = 'product_lists_'.md5(json_encode($queryParams));

        return Cache::remember($cacheKey, 3600, function () use ($queryParams): array {
            $query = Product::query()
                ->with(['categories', 'brand', 'tags', 'media'])
                ->where('status', 'active');

            if (! empty($queryParams['category'])) {
                $catIds = $this->categoryRepository->getIdsBySlugs(explode(',', $queryParams['category']));
                if ($catIds !== []) {
                    $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $catIds));
                }
            }

            if (! empty($queryParams['brand'])) {
                $brandIds = $this->brandRepository->getIdsBySlugs(explode(',', $queryParams['brand']));
                if ($brandIds !== []) {
                    $query->whereIn('brand_id', $brandIds);
                }
            }

            if (! empty($queryParams['price'])) {
                [$min, $max] = array_pad(explode('-', $queryParams['price']), 2, PHP_INT_MAX);
                $query->whereBetween('price', [(float) $min, (float) $max]);
            }

            if (! empty($queryParams['sortBy'])) {
                $col   = $queryParams['sortBy'];
                $order = in_array($col, ['title', 'price']) ? 'asc' : 'desc';
                $query->orderBy($col, $order);
            } else {
                $query->orderByDesc('created_at');
            }

            $products = $query->paginate((int) ($queryParams['show'] ?? 6));

            $recent_products = Cache::remember('recent_products_sidebar', 1800, fn () => $this->productRepository->getRecent(3));

            $brands = Cache::remember('active_brands_list', 3600, fn () => $this->brandRepository->getActive());

            return [
                'recent_products' => $recent_products,
                'products'        => $products,
                'brands'          => $brands,
            ];
        });
    }
}
