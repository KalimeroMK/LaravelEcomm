<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Wishlist;

use Modules\Billing\DTOs\WishlistDTO;
use Modules\Billing\Models\Wishlist;
use Modules\Billing\Repository\WishlistRepository;

readonly class CreateWishlistAction
{
    public function __construct(private WishlistRepository $repository)
    {
    }

    public function execute(WishlistDTO $dto): Wishlist
    {
        return $this->repository->create([
            'product_id' => $dto->product_id,
            'user_id' => $dto->user_id,
            'quantity' => $dto->quantity,
            'price' => $dto->price,
            'discount' => $dto->discount,
        ]);
    }
}
