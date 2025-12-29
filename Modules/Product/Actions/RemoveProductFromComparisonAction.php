<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Closure;

readonly class RemoveProductFromComparisonAction
{
    public function __construct(
        private Closure $getStorage,
        private Closure $putStorage
    ) {}

    public function execute(int $productId): array
    {
        $getStorage = $this->getStorage;
        $putStorage = $this->putStorage;
        $compare = $getStorage();
        $compare = array_values(array_diff($compare, [$productId]));
        $putStorage($compare);

        return [
            'product_id' => $productId,
            'comparison_count' => count($compare),
            'products' => $compare,
        ];
    }
}
