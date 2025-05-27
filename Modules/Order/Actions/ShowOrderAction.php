<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Order\Repository\OrderRepository;

readonly class ShowOrderAction
{
    public function __construct(private OrderRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
