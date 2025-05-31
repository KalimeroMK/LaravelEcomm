<?php

declare(strict_types=1);

namespace Modules\Attribute\Actions\AttributeGroup;

use Illuminate\Http\JsonResponse;
use Modules\Attribute\Repository\AttributeGroupRepository;

readonly class DeleteAttributeGroupAction
{
    private AttributeGroupRepository $repository;

    public function __construct(AttributeGroupRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
