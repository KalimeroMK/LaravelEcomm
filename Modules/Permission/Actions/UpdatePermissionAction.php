<?php

declare(strict_types=1);

namespace Modules\Permission\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Permission\DTOs\PermissionDTO;
use Modules\Permission\Repository\PermissionRepository;

readonly class UpdatePermissionAction
{
    public function __construct(private PermissionRepository $repository) {}

    public function execute(PermissionDTO $dto): Model
    {
        return $this->repository->update($dto->id, [
            'name' => $dto->name,
            'guard_name' => $dto->guard_name,
        ]);
    }
}
