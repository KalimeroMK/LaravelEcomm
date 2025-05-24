<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Repository\CouponRepository;

readonly class CreateCouponAction
{
    public function __construct(private CouponRepository $repository)
    {
    }

    public function execute(CouponDTO $dto): Coupon
    {
        return $this->repository->create([
            'code' => $dto->code,
            'discount' => $dto->discount,
            'description' => $dto->description,
            'type' => $dto->type,
            'expires_at' => $dto->expires_at,
        ]);
    }
}
