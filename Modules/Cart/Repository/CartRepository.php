<?php

namespace Modules\Cart\Repository;

use Modules\Cart\Models\Cart;
use Modules\Core\Repositories\Repository;

class CartRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = Cart::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }

    /**
     * @return mixed
     */
    public function show(): mixed
    {
        return $this->model::whereUserId(Auth()->id())->get();
    }

}