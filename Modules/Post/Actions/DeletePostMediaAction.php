<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Exception;
use Modules\Post\Repository\PostRepository;

readonly class DeletePostMediaAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(int $modelId, int $mediaId): void
    {
        $post = $this->repository->findById($modelId);

        if (! $post) {
            throw new Exception("Post not found with ID: {$modelId}");
        }

        $media = $post->media()->where('id', $mediaId)->first();

        if ($media) {
            $media->delete();
        }
    }
}
