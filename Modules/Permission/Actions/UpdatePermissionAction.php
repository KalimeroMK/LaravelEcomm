<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Repository\PermissionRepository;

readonly class UpdatePermissionAction
{
    public function __construct(private PermissionRepository $repository)
    {
    }

    public function execute(int $id, array $data): PermissionDTO
    {
        $permission = $this->repository->update($id, $data);

        return PermissionDTO::fromArray($permission->toArray());
    }
}
