<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\DTOs\RoleListDTO;
use Modules\Role\Repository\RoleRepository;

readonly class GetAllRolesAction
{
    public function __construct(private RoleRepository $repository) {}

    public function execute(): RoleListDTO
    {
        $roles = $this->repository->findAll();

        return new RoleListDTO($roles);
    }
}
