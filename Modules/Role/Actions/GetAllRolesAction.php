<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Illuminate\Support\Collection;
use Modules\Role\Repository\RoleRepository;

readonly class GetAllRolesAction
{
    public function __construct(private RoleRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
