<?php

declare(strict_types=1);

namespace Modules\Post\Actions;

use Illuminate\Support\Collection;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;

readonly class GetAllTagsAction
{
    public function __construct(private TagRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
