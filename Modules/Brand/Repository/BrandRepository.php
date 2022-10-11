<?php

namespace Modules\Brand\Repository;

use Illuminate\Support\Arr;
use Modules\Brand\Models\Brand;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class BrandRepository extends Repository implements SearchInterface
{
    public $model = Brand::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
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
        if (Arr::has($data, 'slug')) {
            $query->where('slug', 'like', '%' . Arr::get($data, 'slug') . '%');
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
