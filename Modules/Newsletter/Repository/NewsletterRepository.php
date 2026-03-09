<?php

declare(strict_types=1);

namespace Modules\Newsletter\Repository;

use Illuminate\Database\Eloquent\Collection;
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
     *
     * @return Collection<int, Newsletter>
     */
    public function findAll(): Collection
    {
        return Newsletter::orderBy('id', 'desc')->get();
    }
}
