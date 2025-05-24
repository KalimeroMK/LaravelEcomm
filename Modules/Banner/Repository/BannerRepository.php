<?php

declare(strict_types=1);

namespace Modules\Banner\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Banner\Models\Banner;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;

class BannerRepository extends EloquentRepository implements SearchInterface
{
    private const LATEST_BANNERS_LIMIT = 3;

    public function __construct()
    {
        parent::__construct(Banner::class);
    }

    /**
     * Return all banners.
     */
    public function all(): Collection
    {
        return (new $this->modelClass)->all();
    }

    /**
     * Search for entries based on filter criteria provided in the `$data` array.
     *
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_'.md5(json_encode($data));

        return Cache::remember($cacheKey, 86400, function () use ($data) {
            $query = (new $this->modelClass)->newQuery();

            foreach (['title', 'slug', 'description', 'status'] as $field) {
                if (!empty($data[$field])) {
                    $query->where($field, 'like', '%'.$data[$field].'%');
                }
            }

            if (!empty($data['all_included'])) {
                return $query->get();
            }

            $orderBy = $data['order_by'] ?? 'id';
            $sort = $data['sort'] ?? 'desc';
            $perPage = Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage());

            return $query->orderBy($orderBy, $sort)->paginate($perPage);
        });
    }
}
