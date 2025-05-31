<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Order\Repository\OrderRepository;

readonly class FindOrdersByUserAction
{
    public function __construct(private OrderRepository $repository) {}

    public function execute(int $userId): LengthAwarePaginator
    {
        return $this->repository->findAllByUser($userId);
    }
}
