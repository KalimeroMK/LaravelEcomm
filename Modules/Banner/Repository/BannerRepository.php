<?php

namespace Modules\Banner\Repository;

use Illuminate\Support\Arr;
use Modules\Banner\Models\Banner;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class BannerRepository extends Repository implements SearchInterface
{
    public $model = Banner::class;

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();

        foreach (['title', 'slug', 'description', 'status'] as $field) {
            if (isset($data[$field])) {
                $query->where($field, 'like', '%' . $data[$field] . '%');
            }
        }

        if (isset($data['all_included']) && $data['all_included']) {
            return $query->get();
        }

        $orderBy = $data['order_by'] ?? 'id';
        $sort = $data['sort'] ?? 'desc';
        $perPage = $data['per_page'] ?? $this->model->getPerPage();

        return $query->orderBy($orderBy, $sort)->paginate($perPage);
    }

}
