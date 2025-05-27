<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionListDTO;
use Modules\Permission\Repository\PermissionRepository;

readonly class GetAllPermissionsAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(): PermissionListDTO
    {
        $permissions = $this->repository->findAll();

        return new PermissionListDTO($permissions);
    }
}
