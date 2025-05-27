<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Modules\Post\DTOs\PostListDTO;
use Modules\Post\Repository\PostRepository;

readonly class GetAllPostsAction
{
    public function __construct(public PostRepository $repository) {}

    public function execute(): PostListDTO
    {
        $posts = $this->repository->findAll();

        return new PostListDTO($posts);
    }
}
