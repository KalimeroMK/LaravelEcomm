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
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
            return $this->coupon_repository->create($data);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
            return $this->coupon_repository->findById($id);
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
            return $this->coupon_repository->findById($id);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
            return $this->coupon_repository->update($id, $data);
    }

    /**
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
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
