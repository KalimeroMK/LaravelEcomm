<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\DTOs\ShippingDTO;
use Modules\Shipping\Repository\ShippingRepository;

readonly class UpdateShippingAction
{
    public function __construct(private ShippingRepository $repository)
    {
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
