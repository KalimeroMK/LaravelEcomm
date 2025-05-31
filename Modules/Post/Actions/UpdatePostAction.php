<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Post\DTOs\PostDTO;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;

readonly class UpdatePostAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(PostDTO $dto): Post|Model
    {
        $post = $this->repository->update($dto->id, [
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
