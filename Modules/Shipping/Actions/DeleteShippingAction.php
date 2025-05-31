<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Shipping\Repository\ShippingRepository;

readonly class DeleteShippingAction
{
    public function __construct(private ShippingRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
