<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Repository\UserRepository;

class GetUserRolesAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(int $userId): array
    {
        $user = $this->repository->findById($userId);

        return $user->roles->pluck('id')->all();
    }
}
