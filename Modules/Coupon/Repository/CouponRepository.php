<?php

declare(strict_types=1);

namespace Modules\Coupon\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Coupon\Models\Coupon;

class CouponRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Coupon::class);
    }

    /**
     * Get all coupons.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->get();
    }
}
