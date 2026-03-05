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
            'first_name' => $dto->first_name,
            'last_name' => $dto->last_name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'country' => $dto->country,
            'city' => $dto->city,
            'state' => $dto->state,
            'address1' => $dto->address1,
            'address2' => $dto->address2,
            'post_code' => $dto->post_code,
        ]);

        return $order;
    }
}
