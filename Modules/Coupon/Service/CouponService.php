<?php

namespace Modules\Coupon\Service;

use Modules\Core\Service\CoreService;
use Modules\Coupon\Repository\CouponRepository;

class CouponService extends CoreService
{
    public CouponRepository $coupon_repository;

    public function __construct(CouponRepository $coupon_repository)
    {
        parent::__construct($coupon_repository);
        $this->coupon_repository = $coupon_repository;
    }

}
