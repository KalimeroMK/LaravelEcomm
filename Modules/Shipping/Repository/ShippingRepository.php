<?php

declare(strict_types=1);

namespace Modules\Shipping\Repository;

use Illuminate\Support\Collection;
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
}
