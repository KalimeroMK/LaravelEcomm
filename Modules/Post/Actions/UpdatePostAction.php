<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Modules\Post\DTOs\PostDTO;
use Modules\Post\Repository\PostRepository;

readonly class UpdatePostAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(PostDTO $dto): PostDTO
    {
        $post = $this->repository->update($dto->id, [
            'title' => $dto->title,
            'slug' => $dto->slug,
            'summary' => $dto->summary,
            'description' => $dto->description,
            'photo' => $dto->photo,
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

        return PostDTO::fromArray($post->toArray() + [
            'categories' => $dto->categories,
            'tags' => $dto->tags,
        ]);
    }
}
