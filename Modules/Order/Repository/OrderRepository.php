<?php

namespace Modules\Order\Repository;

use Illuminate\Support\Facades\Auth;
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
        return $this->model::with('shipping', 'user', 'carts')->paginate(10);
    }
    
    /**
     * @return mixed
     */
    public function findAllByUser(): mixed
    {
        return $this->model::with('shipping', 'user', 'carts')->where('user_id', Auth::id())->paginate(10);
    }
}