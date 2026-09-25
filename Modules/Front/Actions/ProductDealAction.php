<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Brand\Repository\BrandRepository;
use Modules\Product\Repository\ProductRepository;

class ProductDealAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly BrandRepository $brandRepository,
    ) {}

    public function __invoke(): array
    {
        $page = max(1, (int) request()->input('page', 1));
        $generation = (int) Cache::get('product_listing_generation', 1);
        $products = Cache::remember("deal_products_{$generation}_page{$page}", 1800, fn () => $this->productRepository->getDeals(9));

        $recent_products = Cache::remember('recent_products_sidebar', 1800, fn () => $this->productRepository->getRecent(3));

        $brands = Cache::remember('active_brands_list', 3600, fn () => $this->brandRepository->getActive());

        return [
            'recent_products' => $recent_products,
            'products' => $products,
            'brands' => $brands,
        ];
    }
}
