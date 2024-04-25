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

    /**
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->wishlist_repository->create($data);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->wishlist_repository->delete($id);
    }

}
