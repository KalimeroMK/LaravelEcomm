<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Repository\CouponRepository;

class CreateCouponAction
{
    private CouponRepository $repository;

    public function __construct(CouponRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CouponDTO $dto): Coupon
    {
        return $this->repository->create([
            'code' => $dto->code,
            'type' => $dto->type,
            'value' => $dto->value,
            'status' => $dto->status,
        ]);
    }
}
