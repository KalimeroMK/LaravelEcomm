<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Wishlist;

use Modules\Billing\Repository\WishlistRepository;

readonly class DeleteWishlistAction
{
    public function __construct(private WishlistRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }

}
