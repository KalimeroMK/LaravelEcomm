<?php

namespace Modules\Shipping\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Shipping\Models\Shipping;

class ShippingRepository extends Repository
{
    public $model = Shipping::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::orderBy('id', 'DESC')->get();
    }
}