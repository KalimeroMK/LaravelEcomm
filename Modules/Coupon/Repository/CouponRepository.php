<?php

namespace Modules\Coupon\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Coupon\Models\Coupon;

class CouponRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = Coupon::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::get();
    }
}