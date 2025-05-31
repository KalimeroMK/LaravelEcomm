<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Attribute\Repository\AttributeRepository;

readonly class DeleteAttributeAction
{
    public function __construct(private AttributeRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
