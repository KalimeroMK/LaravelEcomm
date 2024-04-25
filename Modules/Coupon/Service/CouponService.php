<?php

namespace Modules\Coupon\Service;

use Modules\Coupon\Repository\CouponRepository;

class CouponService
{
    public CouponRepository $coupon_repository;

    public function __construct(CouponRepository $coupon_repository)
    {
        $this->coupon_repository = $coupon_repository;
    }

    /**
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->coupon_repository->create($data);
    }

    /**
     * @param  int  $id
     *
     * @return mixed|string
     */
    public function edit(int $id): mixed
    {
        return $this->coupon_repository->findById($id);
    }

    /**
     * @param  int  $id
     *
     * @return mixed|string
     */
    public function show(int $id): mixed
    {
        return $this->coupon_repository->findById($id);
    }

    /**
     * Update an existing attribute.
     *
     * @param  int  $id  The attribute ID to update.
     * @param  array<string, mixed>  $data  The data for updating the attribute.
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        return $this->coupon_repository->update($id, $data);
    }


    /**
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->coupon_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        return $this->coupon_repository->findAll();
    }
}
