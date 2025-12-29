<?php

declare(strict_types=1);

namespace Modules\ProductStats\Actions;

use Illuminate\Support\Collection;
use Modules\ProductStats\Events\ProductImpressionRecorded;
use Modules\ProductStats\Models\ProductImpression;

readonly class StoreProductImpressionAction
{
    public function execute(array $productIds, ?int $userId, string $ipAddress): Collection
    {
        $impressions = collect();

        foreach ($productIds as $productId) {
            $impression = ProductImpression::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
            ]);

            $impressions->push($impression);
            event(new ProductImpressionRecorded($impression));
        }

        return $impressions;
    }
}
