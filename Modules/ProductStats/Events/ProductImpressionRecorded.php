<?php

declare(strict_types=1);

namespace Modules\ProductStats\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\ProductStats\Models\ProductImpression;

class ProductImpressionRecorded
{
    use Dispatchable;

    public function __construct(ProductImpression $impression) {}
}
