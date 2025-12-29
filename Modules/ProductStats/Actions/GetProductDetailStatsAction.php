<?php

declare(strict_types=1);

namespace Modules\ProductStats\Actions;

use Modules\Product\Models\Product;

readonly class GetProductDetailStatsAction
{
    public function __construct(private GetProductStatsAction $getProductStatsAction) {}

    public function execute(int $productId, ?string $from = null, ?string $to = null): array
    {
        $product = Product::findOrFail($productId);

        $impressionsQuery = $product->impressions();
        $clicksQuery = $product->clicks();

        if ($from) {
            $impressionsQuery->whereDate('created_at', '>=', $from);
            $clicksQuery->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $impressionsQuery->whereDate('created_at', '<=', $to);
            $clicksQuery->whereDate('created_at', '<=', $to);
        }

        $impressions = $impressionsQuery->orderByDesc('created_at')->limit(30)->get();
        $clicks = $clicksQuery->orderByDesc('created_at')->limit(30)->get();
        $stats = $this->getProductStatsAction->execute($productId);

        return [
            'product' => $product,
            'impressions' => $impressions,
            'clicks' => $clicks,
            'stats' => $stats,
        ];
    }
}
