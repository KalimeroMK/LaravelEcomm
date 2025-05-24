<?php

declare(strict_types=1);

namespace Modules\Tag\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Tag\Models\Tag;

class TagRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Tag::class);
    }

    /**
     * Get all tags ordered by ID descending.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->orderBy('id', 'desc')->get();
    }
}
