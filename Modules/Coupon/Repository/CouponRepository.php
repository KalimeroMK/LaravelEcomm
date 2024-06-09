<?php

namespace Modules\Coupon\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Coupon\Models\Coupon;

class CouponRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Coupon::class;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::get();
    }
}
