<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Repository\UserRepository;

readonly class FindUserAction
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
