<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Repository\PermissionRepository;

readonly class UpdatePermissionAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(PermissionDTO $dto): PermissionDTO
    {
        $permission = $this->repository->update($dto->id, [
            'name' => $dto->name,
            'guard_name' => $dto->guard_name,
        ]);
        return PermissionDTO::fromArray($permission->toArray());
    }
}
