<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\User\Repository\UserRepository;

readonly class DeleteUserAction
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
