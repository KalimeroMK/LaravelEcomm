<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Illuminate\Database\Eloquent\Model;
use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Repository\CouponRepository;

readonly class UpdateCouponAction
{
    public function __construct(private CouponRepository $repository)
    {
    }

    public function execute(CouponDTO $dto): Model
    {
        return $this->repository->update($dto->id, [
            'code' => $dto->code,
            'discount' => $dto->discount,
            'description' => $dto->description,
            'type' => $dto->type,
            'expires_at' => $dto->expires_at,
        ]);
    }
}
