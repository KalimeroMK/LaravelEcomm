<?php

namespace Modules\Attribute\Repository;

use Illuminate\Support\Arr;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class AttributeRepository extends Repository implements SearchInterface
{
    /**
     * The model instance.
     *
     * @var string
     */
    public $model = Attribute::class;

    /**
     * Search for entries based on filter criteria provided in the `$data` array.
     *
     * @param  array<string, mixed>  $data  Associative array where keys are attribute names and values are the filter criteria.
     * @return mixed The result of the query, either a collection or a paginated response.
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();

        $filterableKeys = ['name', 'code', 'type', 'display'];

        foreach ($filterableKeys as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, 'like', '%'.Arr::get($data, $key).'%');
            }
        }

        $boolKeys = ['filterable', 'configurable'];

        foreach ($boolKeys as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, Arr::get($data, $key));
            }
        }

        if ((bool) Arr::get($data, 'all_included') || empty($data)) {
            return $query->get();
        }

        $orderBy = Arr::get($data, 'order_by', 'id');
        $sort = Arr::get($data, 'sort', 'desc');

        $query->orderBy($orderBy, $sort);

        $perPage = Arr::get($data, 'per_page', (new $this->model)->getPerPage());

        return $query->paginate($perPage);
    }
}
