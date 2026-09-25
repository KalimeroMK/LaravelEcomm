<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Brand\Repository\BrandRepository;
use Modules\Product\Repository\ProductRepository;

class ProductSearchAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(array $data): array
    {
        $searchTerm = Arr::get($data, 'search', '');
        $perPage = min(60, max(1, (int) Arr::get($data, 'per_page', 9)));
        $page = max(1, (int) request()->input('page', 1));
        $generation = (int) Cache::get('product_listing_generation', 1);

        $products = Cache::remember(
            'search_products_'.$generation.'_'.md5($searchTerm.'_'.$perPage.'_page'.$page),
            900,
            fn () => $this->productRepository->searchByTerm($searchTerm, $perPage)
        );

        $brands = Cache::remember(
            'search_brands_'.md5($searchTerm),
            1800,
            fn () => $this->brandRepository->searchByTerm($searchTerm)
        );

        $recent_products = Cache::remember(
            'recent_products_sidebar',
            1800,
            fn () => $this->productRepository->getRecent(3)
        );

        return [
            'recent_products' => $recent_products,
            'products' => $products,
            'brands' => $brands,
        ];
    }
}
