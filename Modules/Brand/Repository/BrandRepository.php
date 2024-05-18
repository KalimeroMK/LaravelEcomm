<?php

namespace Modules\Brand\Repository;

use Modules\Brand\Models\Brand;
use Modules\Core\Repositories\Repository;

class BrandRepository extends Repository
{
    /**
     * The model instance.
     *
     * @var string
     *
     */
    public $model = Brand::class;

    /**
     * Search for entries based on filter criteria provided in the `$data` array.
     *
     * @param  array<string, mixed>  $data  Associative array where keys are attribute names and values are the filter criteria.
     * @return mixed The result of the query, either a collection or a paginated response.
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();

        if (empty($data) || (isset($data['all_included']) && $data['all_included'])) {
            return $query->with('products')->get();
        }

        foreach (['title', 'slug', 'status'] as $field) {
            if (isset($data[$field])) {
                $query->where($field, 'like', '%'.$data[$field].'%');
            }
        }

        $orderBy = $data['order_by'] ?? 'id';
        $sort = $data['sort'] ?? 'desc';

        return $query->orderBy($orderBy, $sort)->paginate((new $this->model)->getPerPage());
    }


}
