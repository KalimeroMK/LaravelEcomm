<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Modules\Tag\DTOs\TagDto;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;

readonly class CreateTagAction
{
    public function __construct(private TagRepository $repository) {}

    public function execute(TagDto $dto): Tag
    {
        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
        ]);
    }
}
