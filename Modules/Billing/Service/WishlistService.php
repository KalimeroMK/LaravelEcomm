<?php

namespace Modules\Billing\Service;

use Illuminate\Support\Collection;
use Modules\Billing\Repository\WishlistRepository;
use Modules\Core\Service\CoreService;

class WishlistService extends CoreService
{
    public WishlistRepository $wishlist_repository;

    public function __construct(WishlistRepository $wishlist_repository)
    {
        parent::__construct($wishlist_repository);
    }

    public function getAll(): Collection
    {
        $colum = 'user_id';
        $value = auth()->user()->id;

        return $this->wishlist_repository->findBy($colum, $value);
    }
}
