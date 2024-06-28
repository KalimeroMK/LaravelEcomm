<?php

namespace Modules\Tag\Service;

use Modules\Core\Service\CoreService;
use Modules\Tag\Repository\TagRepository;

class TagService extends CoreService
{
    public TagRepository $tag_repository;

    public function __construct(TagRepository $tag_repository)
    {
        parent::__construct($tag_repository);
        $this->tag_repository = $tag_repository;
    }
}
