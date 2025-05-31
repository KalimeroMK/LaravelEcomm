<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Database\Eloquent\Collection;
use Modules\Post\Repository\PostRepository;

readonly class GetAllPostsAction
{
    public function __construct(public PostRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
