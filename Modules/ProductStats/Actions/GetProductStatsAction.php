<?php

namespace Modules\ProductStats\Actions;


use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;

class GetProductStatsAction
{
    public function execute(int $productId): array
    {
        $clicks = ProductClick::where('product_id', $productId)
            ->count();
        $impressions = ProductImpression::where('product_id', $productId)
            ->count();
        $ctr = $impressions > 0 ? round($clicks / $impressions, 4) : 0;
        return [
            'clicks' => $clicks,
            'impressions' => $impressions,
            'ctr' => $ctr,
        ];
    }
}
