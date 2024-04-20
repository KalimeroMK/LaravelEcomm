<?php

namespace Modules\Brand\Repository;

use Modules\Brand\Models\Brand;
use Modules\Core\Repositories\Repository;

class BrandRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = Brand::class;

    /**
     * @param  array  $data
     *
     * @return mixed
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

        return $query->orderBy($orderBy, $sort)->paginate($this->model->getPerPage());
    }

    public function getActiveBrands()
    {
        return $this->model::where('status', 'active')->orderBy('title')->get();
    }
}
