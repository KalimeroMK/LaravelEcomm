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
        // Merge into an existing open line for the same product instead of
        // inserting a duplicate row on every add-to-cart click. Guest lines
        // (no user) are matched by session instead.
        $existing = Cart::where('product_id', $dto->product_id)
            ->whereNull('order_id')
            ->when(
                $dto->user_id !== null,
                fn ($query) => $query->where('user_id', $dto->user_id),
                fn ($query) => $query->whereNull('user_id')->where('session_id', $dto->session_id)
            )
            ->first();

        if ($existing instanceof Cart) {
            $existing->quantity += max(1, (int) $dto->quantity);
            $existing->amount = $existing->price * $existing->quantity;
            $existing->save();

            return $existing;
        }

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
