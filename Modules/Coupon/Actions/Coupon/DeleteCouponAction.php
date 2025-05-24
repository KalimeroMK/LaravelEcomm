<?php

declare(strict_types=1);

namespace Modules\Coupon\Actions\Coupon;

use Modules\Coupon\Repository\CouponRepository;

readonly class DeleteCouponAction
{
    public function __construct(private CouponRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
