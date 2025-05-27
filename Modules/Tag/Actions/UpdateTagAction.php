<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Tag\DTOs\TagDTO;
use Modules\Tag\Repository\TagRepository;

readonly class UpdateTagAction
{
    public function __construct(
        private TagRepository $repository
    ) {}

    public function execute(TagDTO $dto): Model
    {
        $tag = $this->repository->findById($dto->id);

        $tag->update([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
        ]);

        return $tag->refresh();
    }
}
