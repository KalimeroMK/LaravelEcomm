<?php

declare(strict_types=1);

namespace Modules\Cart\Actions;

use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;

readonly class CreateCartAction
{
    public function __construct(private CartRepository $repository) {}

    public function execute(CartDTO $dto): Cart
    {
        return $this->repository->create([
            'product_id' => $dto->product_id,
            'quantity' => $dto->quantity,
            'user_id' => $dto->user_id,
            'price' => $dto->price,
            'session_id' => $dto->session_id,
            'amount' => $dto->amount ?? ($dto->price * $dto->quantity),
            'order_id' => $dto->order_id,
        ]);
    }
}
