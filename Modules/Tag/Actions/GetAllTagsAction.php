<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Illuminate\Support\Collection;
use Modules\Tag\Repository\TagRepository;

class GetAllTagsAction
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function execute(): Collection
    {
        return $this->tagRepository->findAll();
    }
}
