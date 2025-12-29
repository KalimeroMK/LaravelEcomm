<?php

declare(strict_types=1);

namespace Modules\Tag\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Tag\Repository\TagRepository;

readonly class ShowTagAction
{
    public function __construct(private TagRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
