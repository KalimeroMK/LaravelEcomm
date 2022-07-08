<?php

namespace Modules\Coupon\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
use Modules\Coupon\Models\Coupon;

class CouponRepository
{
    public function getAll(): LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
    {
        return Coupon::orderBy('id', 'DESC')->paginate(10);
    }
    
    /**
     * @param $id
     *
     * @return Model|Coupon|Collection|_IH_Banner_C|array
     */
    public function getById($id): Model|Coupon|Collection|_IH_Banner_C|array
    {
        return Coupon::findOrFail($id);
    }
}