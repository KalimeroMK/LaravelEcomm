<?php

declare(strict_types=1);

namespace Modules\Shipping\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Shipping\Models\Shipping;

class ShippingRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Shipping::class);
    }

    /**
     * Get all shipping methods ordered by ID descending.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->orderBy('id', 'desc')->get();
    }

    /**
     * Create a new shipping method and invalidate the Helper::shipping() cache.
     */
    public function create(array $data): Model
    {
        $model = parent::create($data);
        Cache::forget('shipping_list');

        return $model;
    }

    /**
     * Update a shipping method and invalidate the Helper::shipping() cache.
     */
    public function update(int $id, array $data): Model
    {
        $model = parent::update($id, $data);
        Cache::forget('shipping_list');

        return $model;
    }

    /**
     * Delete a shipping method and invalidate the Helper::shipping() cache.
     */
    public function destroy(int $id): void
    {
        parent::destroy($id);
        Cache::forget('shipping_list');
    }
}
