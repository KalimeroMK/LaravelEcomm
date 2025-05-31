<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Product\Repository\ProductRepository;

readonly class DeleteProductAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
