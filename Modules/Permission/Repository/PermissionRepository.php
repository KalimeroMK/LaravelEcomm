<?php

namespace Modules\Permission\Repository;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Permission\Models\Permission;

class PermissionRepository extends Repository implements SearchInterface
{
    public $model = Permission::class;

    /**
     * Search for products based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_permissions_'.(json_encode($data));

        return Cache::store('redis')->remember($cacheKey, 86400, function () use ($data) {
            $query = $this->model::query();

            $searchableFields = [
                'name',
                'guard_name',
            ];

            foreach ($searchableFields as $field) {
                if (Arr::has($data, $field)) {
                    $query->where($field, 'like', '%'.Arr::get($data, $field).'%');
                }
            }

            $query->orderBy(
                Arr::get($data, 'order_by', 'id'),
                Arr::get($data, 'sort', 'desc')
            );

            return $query->with('roles')->paginate(
                Arr::get($data, 'per_page', (new Permission)->getPerPage()) //
            );
        });
    }
}
