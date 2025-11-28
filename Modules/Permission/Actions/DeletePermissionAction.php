<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Modules\Permission\Repository\PermissionRepository;

readonly class DeletePermissionAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
