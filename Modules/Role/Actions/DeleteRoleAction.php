<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Modules\Role\Repository\RoleRepository;

readonly class DeleteRoleAction
{
    public function __construct(private RoleRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
