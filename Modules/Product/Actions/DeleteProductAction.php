<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\Repository\ProductRepository;

readonly class DeleteProductAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
