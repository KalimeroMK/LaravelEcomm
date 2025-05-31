<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Post\Repository\PostRepository;

readonly class FindPostAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
