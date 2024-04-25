<?php

namespace Modules\Bundle\Repository;

use Modules\Bundle\Models\Bundle;
use Modules\Core\Repositories\Repository;

class BundleRepository extends Repository
{
    /**
     * The model instance.
     *
     * @var string
     *
     */
    public $model = Bundle::class;


    public function findAll(): mixed

    {
        return $this->model::with(['products', 'media'])->get();
    }

    /**
     * @param  $id
     *
     * @return mixed
     */
    public function findById($id): mixed
    {
        return $this->model::with('media')->find($id);
    }
}