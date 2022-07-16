<?php

namespace Modules\Cart\Repository;

use Modules\Cart\Models\Cart;
use Modules\Core\Repositories\Repository;

class CartRepository extends Repository
{
    public $model = Cart::class;
    
}