<?php

declare(strict_types=1);

namespace Modules\Banner\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Modules\Banner\Models\Banner;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;

class BannerRepository extends Repository implements SearchInterface
{
    private const LATEST_BANNERS_LIMIT = 3;

    /**
     * The model instance.
     *
     * @var string
     */
    public $model = Banner::class;

    /**
     * Return all banners.
     */
    public function all(): Collection
    {
        return $this->model::all();
    }

    /**
     * Search for entries based on filter criteria provided in the `$data` array.
     *
     * @param  array<string, mixed>  $data  Associative array where keys are attribute names and values are the filter criteria.
     * @return mixed The result of the query, either a collection or a paginated response.
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_'.json_encode($data);

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
            $perPage = Arr::get($data, 'per_page', (new Banner)->getPerPage());

            return $query->orderBy($orderBy, $sort)->paginate($perPage);
        });
    }
}
