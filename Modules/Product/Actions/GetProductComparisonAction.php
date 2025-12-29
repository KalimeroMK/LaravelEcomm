<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Closure;
use Illuminate\Support\Collection;
use Modules\Product\Models\Product;

readonly class GetProductComparisonAction
{
    public function __construct(
        private Closure $getStorage
    ) {}

    public function execute(): Collection
    {
        $getStorage = $this->getStorage;
        $productIds = $getStorage();

        return Product::with(['media', 'attributeValues.attribute', 'brand', 'categories'])
            ->whereIn('id', $productIds)
            ->get();
    }
}
