<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Post\DTOs\PostDTO;
use Modules\Post\Repository\PostRepository;

readonly class UpdatePostAction
{
    public function __construct(private PostRepository $repository) {}

    public function execute(PostDTO $dto): Model
    {
        return $this->repository->update($dto->id, [
            'title' => $dto->title,
            'slug' => $dto->slug,
            'summary' => $dto->summary,
            'description' => $dto->description,
            'status' => $dto->status,
            'user_id' => $dto->user_id,
        ]);
    }
}
