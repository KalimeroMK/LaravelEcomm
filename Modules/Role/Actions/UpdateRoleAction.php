<?php

declare(strict_types=1);

namespace Modules\Role\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Role\DTOs\RoleDTO;
use Modules\Role\Repository\RoleRepository;

readonly class UpdateRoleAction
{
    public function __construct(private RoleRepository $repository) {}

    public function execute(int $id, RoleDTO $dto): Model
    {
        return $this->repository->update($id, [
            'name' => $dto->name,
        ]);
    }
}
