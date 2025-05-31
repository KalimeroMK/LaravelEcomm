<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Modules\Post\DTOs\PostDTO;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;

readonly class CreatePostAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(PostDTO $dto): Post
    {
        $post = $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'summary' => $dto->summary,
            'description' => $dto->description,
            'status' => $dto->status,
            'user_id' => $dto->user_id,
        ]);

        // Sync relationships if needed
        if (! empty($dto->categories)) {
            $post->categories()->sync($dto->categories);
        }
        if (! empty($dto->tags)) {
            $post->tags()->sync($dto->tags);
        }

        return $post;
    }
}
