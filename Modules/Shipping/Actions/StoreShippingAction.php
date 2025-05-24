<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Repository\ShippingRepository;

readonly class StoreShippingAction
{
    public function __construct(private ShippingRepository $repository)
    {
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
