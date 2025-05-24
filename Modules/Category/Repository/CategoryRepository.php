<?php

declare(strict_types=1);

namespace Modules\Category\Repository;

use Modules\Category\Models\Category;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class CategoryRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Category::class);
    }
}
