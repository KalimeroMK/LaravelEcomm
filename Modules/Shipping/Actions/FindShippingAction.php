<?php

declare(strict_types=1);

namespace Modules\Shipping\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Shipping\Repository\ShippingRepository;

readonly class FindShippingAction
{
    public function __construct(private ShippingRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
