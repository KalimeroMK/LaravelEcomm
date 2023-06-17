<?php

namespace Modules\Billing\Service;

use Modules\Billing\Repository\WishlistRepository;

class WishlistService
{

    public WishlistRepository $wishlist_repository;

    public function __construct(WishlistRepository $wishlist_repository)
    {
        $this->wishlist_repository = $wishlist_repository;
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
            return $this->wishlist_repository->findAll();
    }

    public function store($data)
    {
            return $this->wishlist_repository->create($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return void
     */
    public function destroy($id)
    {
            $this->wishlist_repository->delete($id);
    }

}
