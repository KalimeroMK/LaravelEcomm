<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Illuminate\Support\Collection;
use Modules\Shipping\Repository\ShippingRepository;

readonly class GetAllShippingAction
{
    public function __construct(private ShippingRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
