<?php

namespace Modules\Bundle\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
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


    public function findAll(): Collection

    {
        return $this->model::with(['products', 'media'])->get();
    }

    /**
     * @param  $id
     *
     * @return Model|null
     */
    public function findById($id): ?Model
    {
        return $this->model::with('media')->find($id);
    }
}