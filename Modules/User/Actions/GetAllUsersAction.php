<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\DTOs\UserListDTO;
use Modules\User\Repository\UserRepository;

readonly class GetAllUsersAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(): UserListDTO
    {
        $users = $this->repository->findAll();

        return new UserListDTO($users);
    }
}
