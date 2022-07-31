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
        return $this->model::with('brand', 'categories', 'carts', 'condition', 'sizes')->paginate(10);
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
        $item->categories()->attach($data['category']);
        $item->sizes()->attach($data['size']);
        
        return $item->fresh();
    }
    
}
