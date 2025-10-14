<?php

declare(strict_types=1);

namespace Modules\Attribute\Repository;

use Illuminate\Support\Arr;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;

class AttributeRepository extends EloquentRepository implements SearchInterface
{
    public function __construct()
    {
        parent::__construct(Attribute::class);
    }

    /**
     * Search for entries based on filter criteria.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $query = Attribute::query();

        $filterableKeys = ['name', 'code', 'type', 'display'];

        foreach ($filterableKeys as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, 'like', '%'.Arr::get($data, $key).'%');
            }
        }

        $boolKeys = ['is_filterable', 'is_configurable'];

        foreach ($boolKeys as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, Arr::get($data, $key));
            }
        }

        if (Arr::get($data, 'all_included') || $data === []) {
            return $query->get();
        }

        $orderBy = Arr::get($data, 'order_by', 'id');
        $sort = Arr::get($data, 'sort', 'desc');

        $query->orderBy($orderBy, $sort);

        $perPage = Arr::get($data, 'per_page', 15);

        return $query->paginate($perPage);
    }
}
