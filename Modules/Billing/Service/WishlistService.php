<?php

namespace Modules\Billing\Service;

use Exception;
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
        try {
            return $this->wishlist_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    public function store($data)
    {
        try {
            return $this->wishlist_repository->create($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
        try {
            $this->wishlist_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
}