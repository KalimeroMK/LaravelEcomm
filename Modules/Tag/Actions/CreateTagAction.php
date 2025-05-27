<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Modules\Tag\DTOs\TagDto;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;

readonly class CreateTagAction
{
    private TagRepository $repository;

    public function __construct(TagRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(array $attributes): Tag
    {
        $dto = new TagDto($attributes);

        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'status' => $dto->status,
        ]);
    }
}
