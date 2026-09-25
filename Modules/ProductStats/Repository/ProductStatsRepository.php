<?php

declare(strict_types=1);

namespace Modules\ProductStats\Repository;

use Illuminate\Support\Collection;
use Modules\Product\Models\Product;
use Modules\ProductStats\DTOs\ProductStatsDTO;

class ProductStatsRepository
{
    /**
     * Get filtered product stats.
     *
     * @param  array  $filters  ['from' => ?, 'to' => ?, 'category_id' => ?, 'order_by' => ?, 'sort' => ?]
     */
    public function getProductStats(array $filters = []): Collection
    {
        $sort = $filters['sort'] ?? 'desc';
        $orderBy = $filters['order_by'] ?? 'id';

        $query = Product::with(['media', 'categories']);

        if (! empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters): void {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        if (! empty($filters['from'])) {
            $query->whereDate('products.created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->whereDate('products.created_at', '<=', $filters['to']);
        }

        $query->orderBy($orderBy, $sort);

        // Two withCount subqueries instead of two COUNT queries per product.
        $query->withCount(['clicks', 'impressions']);

        /** @var Collection $products */
        $products = $query->get();

        return $products->map(function (Product $product): ProductStatsDTO {
            $clicks = (int) $product->clicks_count;
            $impressions = (int) $product->impressions_count;
            $ctr = $impressions > 0 ? round($clicks / $impressions, 4) : 0;

            return new ProductStatsDTO($product, $impressions, $clicks, $ctr);
        });
    }
}
