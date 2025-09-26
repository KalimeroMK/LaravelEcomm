<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Repository\PermissionRepository;
use Modules\Permission\Models\Permission;

readonly class CreatePermissionAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(PermissionDTO $dto): Permission
    {
        return $this->repository->create([
            'name' => $dto->name,
            'guard_name' => $dto->guard_name,
        ]);
    }
}
