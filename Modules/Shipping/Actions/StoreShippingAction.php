<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Repository\ShippingRepository;

class StoreShippingAction
{
    private ShippingRepository $repository;

    public function __construct(ShippingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ShippingDTO $dto): ShippingDTO
    {
        $shipping = $this->repository->create([
            'type' => $dto->type,
            'price' => $dto->price,
            'status' => $dto->status,
        ]);

        return ShippingDTO::fromArray($shipping->toArray());
    }
}
