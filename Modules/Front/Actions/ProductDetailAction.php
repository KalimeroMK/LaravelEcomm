<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use Illuminate\Support\Facades\Cache;
use Modules\Product\Models\Product;
use Modules\Product\Services\RecentlyViewedService;

class ProductDetailAction
{
    public function __construct(
        private readonly RecentlyViewedService $recentlyViewedService
    ) {}

    public function __invoke(string $slug): array
    {
        $cacheKey = 'productDetail_'.$slug;

        $result = Cache::remember($cacheKey, 24 * 60, function () use ($slug): array {
            $product_detail = Product::getProductBySlug($slug);

            $related = Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
                ->whereHas('categories', function ($q) use ($product_detail): void {
                    $q->whereIn('title', $product_detail->categories->pluck('title'));
                })
                ->where('id', '!=', $product_detail->id)
                ->where('status', 'active')
                ->limit(8)
                ->get();

            return [
                'product_detail' => $product_detail,
                'related' => $related,
            ];
        });
        
        // Track recently viewed product
        if (isset($result['product_detail'])) {
            try {
                $this->recentlyViewedService->addProduct(
                    $result['product_detail']->id,
                    auth()->id()
                );
            } catch (Exception $e) {
                // Silently fail if session not available
            }
        }
        
        return $result;
    }
}
