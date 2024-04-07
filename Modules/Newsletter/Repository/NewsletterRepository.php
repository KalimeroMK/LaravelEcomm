<?php

namespace Modules\Newsletter\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Newsletter\Models\Newsletter;

class NewsletterRepository extends Repository
{
    public $model = Newsletter::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }
}
