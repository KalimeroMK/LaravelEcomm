<?php

declare(strict_types=1);

namespace Modules\Product\DTOs;

class ProductListDTO
{
    public array $products;

    public function __construct($products)
    {
        $this->products = $products->toArray();
    }
}
