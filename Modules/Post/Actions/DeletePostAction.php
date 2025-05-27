<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Modules\Post\Repository\PostRepository;

readonly class DeletePostAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
