<?php

namespace Modules\Banner\Repository;

use Illuminate\Support\Facades\Cache;
use Modules\Banner\Models\Banner;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class BannerRepository extends Repository implements SearchInterface
{
    public $model = Banner::class;
    private const LATEST_BANNERS_LIMIT = 3;

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_'.md5(json_encode($data));

        // Cache for 24 hours (86400 seconds)
        return Cache::remember($cacheKey, 86400, function () use ($data) {
            $query = $this->model::query();

            foreach (['title', 'slug', 'description', 'status'] as $field) {
                if (isset($data[$field])) {
                    $query->where($field, 'like', '%'.$data[$field].'%');
                }
            }

            if (isset($data['all_included']) && $data['all_included']) {
                return $query->get();
            }

            $orderBy = $data['order_by'] ?? 'id';
            $sort = $data['sort'] ?? 'desc';
            $perPage = $data['per_page'] ?? $this->model->getPerPage();

            return $query->orderBy($orderBy, $sort)->paginate($perPage);
        });
    }

    /**
     * @return mixed
     */
    public function getActiveBanners(): mixed
    {
        $cacheKey = 'active_banners';

        // Cache for 24 hours (86400 seconds)
        return Cache::remember($cacheKey, 86400, function () {
            return $this->model::where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(self::LATEST_BANNERS_LIMIT)
                ->get();
        });
    }

}
