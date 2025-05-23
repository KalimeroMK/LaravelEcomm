<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductListDTO;
use Modules\Product\Models\Product;

class GetAllProductsAction
{
    public function execute(): ProductListDTO
    {
        $products = Product::with(['categories', 'tags', 'brand', 'attributes.attribute', 'author'])->get();

        return new ProductListDTO($products);
    }
}
