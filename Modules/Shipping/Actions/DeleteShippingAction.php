<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Modules\Shipping\Repository\ShippingRepository;

readonly class DeleteShippingAction
{
    public function __construct(private ShippingRepository $repository) {}

    public function execute(int $id): bool
    {
        return $this->repository->destroy($id);
    }
}
