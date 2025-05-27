<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Tag\Repository\TagRepository;

class ShowTagAction
{
    private TagRepository $tagRepository;

    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    public function execute(array|int $id): Model
    {
        return $this->tagRepository->findById($id);
    }
}
