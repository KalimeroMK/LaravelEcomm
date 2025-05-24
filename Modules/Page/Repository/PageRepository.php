<?php

declare(strict_types=1);

namespace Modules\Page\Repository;

use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Page\Models\Page;

class PageRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Page::class);
    }
}
