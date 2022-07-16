<?php

namespace Modules\Product\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Product\Models\Product;

class ProductRepository extends Repository
{
    public $model = Product::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::with('brand', 'categories', 'carts')->paginate(10);
    }
    
}
