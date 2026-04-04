<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Repository\BrandRepository;
use Modules\Product\Repository\ProductRepository;

class ProductBrandAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(string $slug): array
    {
        $products = $this->productRepository->getByBrand($slug, 9);

        $brands = Cache::remember('active_brands_list', 3600, fn () => $this->brandRepository->getActive());

        $recentProducts = Cache::remember('recent_products_sidebar', 1800, fn () => $this->productRepository->getRecent(3));

        return [
            'products'       => $products,
            'brands'         => $brands,
            'recentProducts' => $recentProducts,
        ];
    }
}
