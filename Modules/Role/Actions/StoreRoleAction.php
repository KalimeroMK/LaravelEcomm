<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Repository\RoleRepository;

readonly class StoreRoleAction
{
    public function __construct(private RoleRepository $repository)
    {
    }

    public function execute(array $data): RoleDTO
    {
        $role = $this->repository->create(['name' => $data['name']]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return RoleDTO::fromArray($role->fresh('permissions')->toArray());
    }
}
