<?php

declare(strict_types=1);

namespace Modules\Banner\Repository;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Modules\Banner\Models\Banner;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;

class BannerRepository extends EloquentRepository implements SearchInterface
{
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
     * Return currently active banners with categories and media eager-loaded.
     * Uses the scopeActive() DB-level filter to avoid loading all banners into PHP.
     */
    public function getActive(): Collection
    {
        return (new $this->modelClass)
            ->active()
            ->with(['categories', 'media'])
            ->get();
    }

    /**
     * Search for entries based on filter criteria provided in the `$data` array.
     * No caching — used by admin which always needs fresh data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $query = (new $this->modelClass)->newQuery();

        foreach (['title', 'slug', 'description', 'status'] as $field) {
            if (! empty($data[$field])) {
                $query->where($field, 'like', '%'.$data[$field].'%');
            }
        }

        if (! empty($data['all_included'])) {
            return $query->get();
        }

        $orderBy = $data['order_by'] ?? 'id';
        $sort = $data['sort'] ?? 'desc';
        $perPage = Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage());

        return $query->orderBy($orderBy, $sort)->paginate($perPage);
    }
}
