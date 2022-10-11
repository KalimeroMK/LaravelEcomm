<?php

namespace Modules\Product\Repository;

use Illuminate\Support\Arr;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Product\Models\Product;

class ProductRepository extends Repository implements SearchInterface
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
    
    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();
        if (Arr::has($data, 'title')) {
            $query->where('title', 'like', '%' . Arr::get($data, 'title') . '%');
        }
        if (Arr::has($data, 'summary')) {
            $query->where('summary', 'like', '%' . Arr::get($data, 'summary') . '%');
        }
        if (Arr::has($data, 'description')) {
            $query->where('description', 'like', '%' . Arr::get($data, 'description') . '%');
        }
        if (Arr::has($data, 'color')) {
            $query->where('color', 'like', '%' . Arr::get($data, 'color') . '%');
        }
        if (Arr::has($data, 'stock')) {
            $query->where('stock', 'like', '%' . Arr::get($data, 'stock') . '%');
        }
        if (Arr::has($data, 'brand_id')) {
            $query->where('brand_id', 'like', '%' . Arr::get($data, 'brand_id') . '%');
        }
        if (Arr::has($data, 'price')) {
            $query->where('price', 'like', '%' . Arr::get($data, 'price') . '%');
        }
        if (Arr::has($data, 'discount')) {
            $query->where('discount', 'like', '%' . Arr::get($data, 'discount') . '%');
        }
        if (Arr::has($data, 'status')) {
            $query->where('status', 'like', '%' . Arr::get($data, 'status') . '%');
        }
        if (Arr::has($data, 'all_included') && (bool)Arr::get($data, 'all_included') === true) {
            return $query->get();
        }
        $query->orderBy(Arr::get($data, 'order_by') ?? 'id', Arr::get($data, 'sort') ?? 'desc');
        
        return $query->paginate(Arr::get($data, 'per_page') ?? (new $this->model)->getPerPage());
    }
    
}
