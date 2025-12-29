<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Closure;
use Modules\Product\Models\Product;

readonly class AddProductToComparisonAction
{
    public function __construct(
        private Closure $getStorage,
        private Closure $putStorage
    ) {}

    public function execute(int $productId): array
    {
        Product::findOrFail($productId);

        $getStorage = $this->getStorage;
        $putStorage = $this->putStorage;
        $compare = $getStorage();

        if (! in_array($productId, $compare)) {
            $compare[] = $productId;
            // Keep last 4 products
            $compare = array_slice($compare, -4);
            $putStorage($compare);
        }

        return [
            'product_id' => $productId,
            'comparison_count' => count($compare),
            'products' => $compare,
        ];
    }
}
