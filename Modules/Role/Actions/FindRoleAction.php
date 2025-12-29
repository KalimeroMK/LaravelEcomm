<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Role\Repository\RoleRepository;

readonly class FindRoleAction
{
    public function __construct(private RoleRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
