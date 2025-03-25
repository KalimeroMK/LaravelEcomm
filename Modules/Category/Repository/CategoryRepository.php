<?php

declare(strict_types=1);

namespace Modules\Category\Repository;

use Modules\Category\Models\Category;
use Modules\Core\Repositories\Repository;

class CategoryRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Category::class;
}
