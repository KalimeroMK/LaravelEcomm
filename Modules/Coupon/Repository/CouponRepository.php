<?php

declare(strict_types=1);

namespace Modules\Coupon\Repository;

use Illuminate\Database\Eloquent\Collection;
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
     *
     * @return Collection<int, Coupon>
     */
    public function findAll(): Collection
    {
        return Coupon::orderBy('id', 'desc')->get();
    }
}
