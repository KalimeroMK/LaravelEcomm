<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Repository\ShippingRepository;

class UpdateShippingAction
{
    private ShippingRepository $repository;

    public function __construct(ShippingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, ShippingDTO $dto): void
    {
        $this->repository->update($id, [
            'type' => $dto->type,
            'price' => $dto->price,
            'status' => $dto->status,
        ]);
    }
}
