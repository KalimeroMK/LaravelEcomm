<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;

class StoreOrderAction
{
    private OrderRepository $repository;

    public function __construct(OrderRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(OrderDTO $dto): Order
    {
        /** @var Order $order */
        $order = $this->repository->create([
            'order_number' => $dto->order_number,
            'user_id' => $dto->user_id,
            'sub_total' => $dto->sub_total,
            'shipping_id' => $dto->shipping_id,
            'total_amount' => $dto->total_amount,
            'quantity' => $dto->quantity,
            'payment_method' => $dto->payment_method,
            'payment_status' => $dto->payment_status,
            'status' => $dto->status,
            'payer_id' => $dto->payer_id,
            'transaction_reference' => $dto->transaction_reference,
        ]);

        return $order;
    }
}
