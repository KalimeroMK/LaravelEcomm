<?php

declare(strict_types=1);

namespace Modules\Bundle\Actions;

use Modules\Product\Repository\ProductRepository;

readonly class GetAllProductsAction
{
    private ProductRepository $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute()
    {
        return $this->repository->findAll();
    }
}
