<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Illuminate\Database\Eloquent\Model;
use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Repository\CouponRepository;

class UpdateCouponAction
{
    private CouponRepository $repository;

    public function __construct(CouponRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CouponDTO $dto): Model
    {
        $coupon = $this->repository->findById($dto->id);
        $coupon->update([
            'code' => $dto->code,
            'discount' => $dto->discount,
            'description' => $dto->description,
            'type' => $dto->type,
            'expires_at' => $dto->expires_at,
        ]);

        return $coupon;
    }
}
