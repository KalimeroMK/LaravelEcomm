<?php

declare(strict_types=1);

namespace Modules\ProductStats\DTOs;

use Modules\Product\Models\Product;

readonly class ProductStatsDTO
{
    public function __construct(
        public Product $product,
        public int $impressions,
        public int $clicks,
        public float $ctr
    ) {}
}
