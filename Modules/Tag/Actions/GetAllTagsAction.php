<?php

namespace Modules\Tag\Actions;

use Modules\Tag\Repository\TagRepository;
use Illuminate\Support\Collection;

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
