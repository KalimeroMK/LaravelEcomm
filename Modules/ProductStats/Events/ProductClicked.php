<?php

declare(strict_types=1);

namespace Modules\ProductStats\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Modules\ProductStats\Models\ProductClick;

class ProductClicked
{
    use Dispatchable;

    public function __construct(public ProductClick $click) {}
}
