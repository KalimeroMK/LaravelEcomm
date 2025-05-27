<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\Repository\OrderRepository;

readonly class DeleteOrderAction
{
    public function __construct(private OrderRepository $repository)
    {
    }

    public function execute(int $id): bool
    {
        $this->repository->destroy($id);
    }
}
