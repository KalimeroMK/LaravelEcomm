<?php

namespace Modules\Shipping\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Shipping\Models\Shipping;

class ShippingRepository extends Repository
{
    public $model = Shipping::class;

    /**
     * @return object
     */
    public function findAll(): object
    {
        return $this->model::orderBy('id', 'DESC')->get();
    }
}