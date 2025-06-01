<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Illuminate\Http\JsonResponse;
use Modules\Permission\Repository\PermissionRepository;

readonly class DeletePermissionAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(int $id): JsonResponse
    {
        $this->repository->destroy($id);

        return response()->json();
    }
}
