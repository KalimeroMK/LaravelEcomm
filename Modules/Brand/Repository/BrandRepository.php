<?php

declare(strict_types=1);

namespace Modules\Brand\Repository;

use Modules\Brand\Models\Brand;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;

class BrandRepository extends EloquentRepository implements EloquentRepositoryInterface, SearchInterface
{
    public function __construct()
    {
        parent::__construct(Brand::class);
    }

    /**
     * Search for entries based on filter criteria.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $query = (new $this->modelClass)->newQuery();

        if ($data === [] || ! empty($data['all_included'])) {
            return $query->with('products')->get();
        }

        foreach (['title', 'slug', 'status'] as $field) {
            if (! empty($data[$field])) {
                $query->where($field, 'like', '%'.$data[$field].'%');
            }
        }

        $orderBy = $data['order_by'] ?? 'id';
        $sort = $data['sort'] ?? 'desc';
        $perPage = (new $this->modelClass)->getPerPage();

        return $query->orderBy($orderBy, $sort)->paginate($perPage);
    }
}
