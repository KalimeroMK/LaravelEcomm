<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Repository\ShippingRepository;

readonly class StoreShippingAction
{
    private ShippingRepository $repository;

    public function __construct(ShippingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ShippingDTO $dto): Shipping
    {
        return $this->repository->create([
            'type' => $dto->type,
            'price' => $dto->price,
            'status' => $dto->status,
        ]);
    }
}
