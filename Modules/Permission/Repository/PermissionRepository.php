<?php

declare(strict_types=1);

namespace Modules\Permission\Repository;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Permission\Models\Permission;

class PermissionRepository extends EloquentRepository implements EloquentRepositoryInterface, SearchInterface
{
    public function __construct()
    {
        parent::__construct(Permission::class);
    }

    /**
     * Search for permissions based on given criteria.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_permissions_'.md5(json_encode($data));

        return Cache::store('redis')->remember($cacheKey, 86400, function () use ($data) {
            $query = (new $this->modelClass)->newQuery();

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
                Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage())
            );
        });
    }
}
