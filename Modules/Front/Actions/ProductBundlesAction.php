<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Product\Repository\ProductRepository;

class ProductBundlesAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly BundleRepository $bundleRepository,
    ) {}

    public function __invoke(): array
    {
        $recent_products = Cache::remember('recent_products_sidebar', 1800, fn () => $this->productRepository->getRecent(3));

        $bundles = $this->bundleRepository->paginate((int) request('show', 6));

        return [
            'recent_products' => $recent_products,
            'bundles'         => $bundles,
            'products'        => $bundles,
        ];
    }
}
