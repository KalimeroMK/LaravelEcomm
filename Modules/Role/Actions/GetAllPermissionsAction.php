<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Illuminate\Support\Collection;
use Modules\Permission\Repository\PermissionRepository;

readonly class GetAllPermissionsAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
