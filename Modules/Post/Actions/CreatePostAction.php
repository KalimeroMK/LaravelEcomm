<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
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

        // Sync relations
        SyncRelations::execute($post, [
            'categories' => $dto->categories,
            'tags' => $dto->tags,
        ]);

        // Upload media
        MediaUploader::uploadMultiple($post, ['images'], 'post');

        return $post;
    }
}
