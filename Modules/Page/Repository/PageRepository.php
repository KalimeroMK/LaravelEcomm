<?php

namespace Modules\Page\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Page\Models\Page;

class PageRepository extends Repository
{
    /**
     * The model that the repository works with.
     *
     * @var string
     */
    public $model = Page::class;
}
