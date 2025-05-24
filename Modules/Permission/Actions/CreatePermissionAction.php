<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Repository\PermissionRepository;

readonly class CreatePermissionAction
{
    public function __construct(private PermissionRepository $repository)
    {
    }

    public function execute(array $data): PermissionDTO
    {
        $permission = $this->repository->create($data);

        return new PermissionDTO($permission);
    }
}
