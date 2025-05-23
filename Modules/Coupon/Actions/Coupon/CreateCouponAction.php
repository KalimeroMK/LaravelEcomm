<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Models\Coupon;

readonly class CreateCouponAction
{
    public function execute(CouponDTO $dto): Coupon
    {
        return Coupon::create([
            'code' => $dto->code,
            'discount' => $dto->discount,
            'description' => $dto->description,
            'type' => $dto->type,
            'expires_at' => $dto->expires_at,
        ]);
    }
}
