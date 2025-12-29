<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Permission\Repository\PermissionRepository;

readonly class FindPermissionAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
