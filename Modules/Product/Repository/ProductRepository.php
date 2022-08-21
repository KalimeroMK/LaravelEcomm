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
        return $this->model::with('brand', 'categories', 'carts', 'condition', 'sizes', 'tags')->get();
    }
    
    /**
     * @param  int  $id
     * @param  array  $data
     *
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();
        
        return $item->fresh();
    }
    
    /**
     * @param  int  $id
     *
     * @return mixed
     */
    public function findById(int $id): mixed
    {
        return $this->model::with('brand', 'categories', 'carts', 'condition', 'sizes', 'tags')->find($id);
    }
    
}
