<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Illuminate\Support\Collection;
use Modules\Order\Repository\OrderRepository;

readonly class GetAllOrdersAction
{
    public function __construct(private OrderRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
