<?php

declare(strict_types=1);

namespace Modules\Newsletter\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Newsletter\Models\Newsletter;

class NewsletterRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Newsletter::class);
    }

    /**
     * Get all newsletter entries.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->get();
    }
}
