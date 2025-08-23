<?php

declare(strict_types=1);

namespace Modules\Brand\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Brand\Repository\BrandRepository;

readonly class DeleteBrandAction
{
    public function __construct(
        private BrandRepository $repository
    ) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
