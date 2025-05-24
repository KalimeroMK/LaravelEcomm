<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductListDTO;
use Modules\Product\Repository\ProductRepository;

readonly class GetAllProductsAction
{
    public function __construct(private ProductRepository $repository)
    {
    }

    public function execute(): ProductListDTO
    {
        $products = $this->repository->findAll();

        return new ProductListDTO($products);
    }
}
