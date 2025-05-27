<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingListDTO;
use Modules\Shipping\Repository\ShippingRepository;

readonly class GetAllShippingAction
{
    public function __construct(private ShippingRepository $repository) {}

    public function execute(): ShippingListDTO
    {
        $shipping = $this->repository->findAll();

        return new ShippingListDTO($shipping);
    }
}
