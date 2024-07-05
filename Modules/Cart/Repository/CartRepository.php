<?php

namespace Modules\Cart\Repository;

use Illuminate\Support\Collection;
use Modules\Cart\Models\Cart;
use Modules\Core\Repositories\Repository;

class CartRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Cart::class;

    public function findAll(): Collection
    {
        return $this->model::get();
    }

    public function show(): mixed
    {
        return $this->model::whereUserId(Auth()->id())->get();
    }
}
