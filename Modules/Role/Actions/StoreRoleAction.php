<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Models\Role;
use Modules\Role\Repository\RoleRepository;

readonly class StoreRoleAction
{
    public function __construct(private RoleRepository $repository) {}

    public function execute(RoleDTO $dto): Role
    {
        return $this->repository->create([
            'name' => $dto->name,
        ]);
    }
}
