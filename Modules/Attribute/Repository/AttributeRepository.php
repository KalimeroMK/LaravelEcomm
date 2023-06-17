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

        $filterableKeys = ['name', 'code', 'type', 'display'];

        foreach ($filterableKeys as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, 'like', '%' . Arr::get($data, $key) . '%');
            }
        }

        $boolKeys = ['filterable', 'configurable'];

        foreach ($boolKeys as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, Arr::get($data, $key));
            }
        }

        if ((bool)Arr::get($data, 'all_included') || empty($data)) {
            return $query->get();
        }

        $orderBy = Arr::get($data, 'order_by', 'id');
        $sort = Arr::get($data, 'sort', 'desc');

        $query->orderBy($orderBy, $sort);

        $perPage = Arr::get($data, 'per_page', (new $this->model)->getPerPage());

        return $query->paginate($perPage);
    }
}
