<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Repository\RoleRepository;

readonly class UpdateRoleAction
{
    public function __construct(private RoleRepository $repository)
    {
    }

    public function execute(int $id, array $data): RoleDTO
    {
        $role = $this->repository->update($id, ['name' => $data['name']]);

        if (isset($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return RoleDTO::fromArray($role->fresh('permissions')->toArray());
    }
}
