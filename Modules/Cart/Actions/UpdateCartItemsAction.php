<?php

declare(strict_types=1);

namespace Modules\Cart\Actions;

use Illuminate\Http\Request;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Repository\CartRepository;

readonly class UpdateCartItemsAction
{
    public function __construct(
        private CartRepository $repository,
        private UpdateCartAction $updateCartAction
    ) {}

    public function execute(Request $request): void
    {
        if ($request->has('quantity') && $request->has('qty_id')) {
            foreach ($request->input('quantity') as $k => $qty) {
                $id = $request->input('qty_id')[$k];
                $cart = $this->repository->findById($id);

                if (! $cart) {
                    continue;
                }

                $updateDto = new CartDTO(
                    id: $cart->id,
                    product_id: $cart->product_id,
                    quantity: (int) $qty,
                    user_id: $cart->user_id,
                    price: $cart->price,
                    session_id: session()->getId(),
                    amount: $cart->price * (int) $qty,
                    order_id: $cart->order_id,
                );

                $this->updateCartAction->execute($updateDto);
            }
        }
    }
}
