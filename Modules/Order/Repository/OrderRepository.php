<?php

namespace Modules\Order\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Order\Models\Order;

class OrderRepository extends Repository
{
    public $model = Order::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::paginate(10);
    }
}