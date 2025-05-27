<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;

class UpdateRoleAction
{
    private RoleRepository $repository;

    public function __construct(RoleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, array $data): RoleDTO
    {
        $role = $this->repository->update($id, ['name' => $data['name']]);
        /** @var Role $role */
        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return RoleDTO::fromArray($role->fresh('permissions')->toArray());
    }
}
