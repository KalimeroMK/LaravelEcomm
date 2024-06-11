<?php

namespace Modules\Shipping\Service;

use Modules\Core\Service\CoreService;
use Modules\Shipping\Repository\ShippingRepository;

class ShippingService extends CoreService
{
    public ShippingRepository $shipping_repository;

    public function __construct(ShippingRepository $shipping_repository)
    {
        parent::__construct($shipping_repository);
        $this->shipping_repository = $shipping_repository;
    }

}
