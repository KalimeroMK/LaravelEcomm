<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Repository\ShippingRepository;

readonly class FindShippingAction
{
    public function __construct(private ShippingRepository $repository) {}

    public function execute(int $id): ShippingDTO
    {
        $shipping = $this->repository->findById($id);

        return ShippingDTO::fromArray($shipping->toArray());
    }
}
