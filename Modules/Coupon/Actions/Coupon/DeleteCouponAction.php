<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Modules\Coupon\Models\Coupon;

readonly class DeleteCouponAction
{
    public function execute(int $id): bool
    {
        $coupon = Coupon::findOrFail($id);

        return $coupon->delete();
    }
}
