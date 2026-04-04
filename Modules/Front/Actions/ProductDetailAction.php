<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Product\Repository\ProductRepository;
use Modules\Product\Services\RecentlyViewedService;

class ProductDetailAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly RecentlyViewedService $recentlyViewedService,
    ) {}

    public function __invoke(string $slug): array
    {
        $product_detail = $this->productRepository->findBySlug($slug);

        // Related products — use category IDs already loaded on the model.
        // Avoids the previous N+1 pattern of pluck('title') inside a whereHas subquery.
        $related = Cache::remember("related_products_{$product_detail->id}", 3600, function () use ($product_detail) {
            $categoryIds = $product_detail->categories->pluck('id')->toArray();

            return $this->productRepository->getRelatedByCategoryIds($categoryIds, $product_detail->id, 8);
        });

        try {
            $this->recentlyViewedService->addProduct($product_detail->id, auth()->id());
        } catch (\Exception) {
            // Session unavailable in some contexts — safe to ignore.
        }

        return [
            'product_detail' => $product_detail,
            'related'        => $related,
        ];
    }
}
