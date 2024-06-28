<?php

namespace Modules\Newsletter\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Newsletter\Models\Newsletter;

class NewsletterRepository extends Repository
{
    public $model = Newsletter::class;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::get();
    }
}
