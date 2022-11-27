<?php

namespace Modules\Attribute\Repository;

use Illuminate\Support\Arr;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class AttributeRepository extends Repository implements SearchInterface
{
    public $model = Attribute::class;
    
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
        if (Arr::has($data, 'name')) {
            $query->where('name', 'like', '%' . Arr::get($data, 'name') . '%');
        }
        if (Arr::has($data, 'code')) {
            $query->where('code', 'like', '%' . Arr::get($data, 'code') . '%');
        }
        if (Arr::has($data, 'type')) {
            $query->where('type', 'like', '%' . Arr::get($data, 'type') . '%');
        }
        if (Arr::has($data, 'display')) {
            $query->where('display', 'like', '%' . Arr::get($data, 'display') . '%');
        }
        if (Arr::has($data, 'filterable')) {
            $query->where('filterable', '==', Arr::get($data, 'filterable'));
        }
        if (Arr::has($data, 'configurable')) {
            $query->where('configurable', '==', Arr::get($data, 'configurable'));
        }
        if (Arr::has($data, 'all_included') && (bool)Arr::get($data, 'all_included') === true || empty($data)) {
            return $query->get();
        }
        
        $query->orderBy(Arr::get($data, 'order_by') ?? 'id', Arr::get($data, 'sort') ?? 'desc');
        
        return $query->paginate(Arr::get($data, 'per_page') ?? (new $this->model)->getPerPage());
    }
}
