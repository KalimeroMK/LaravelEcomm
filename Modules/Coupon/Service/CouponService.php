<?php

namespace Modules\Coupon\Service;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use LaravelIdea\Helper\Modules\Coupon\Models\_IH_Coupon_C;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Repository\CouponRepository;

class CouponService
{
    private CouponRepository $coupon_repository;
    
    public function __construct(CouponRepository $coupon_repository)
    {
        $this->coupon_repository = $coupon_repository;
    }
    
    /**
     * @param $data
     *
     * @return Collection|_IH_Coupon_C|mixed|Coupon|Coupon[]
     */
    public function store($data): mixed
    {
        try {
            return $this->coupon_repository->create($data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id)
    {
        try {
            return $this->coupon_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data)
    {
        try {
            return $this->coupon_repository->update($id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
        try {
            $this->coupon_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function index()
    {
        try {
            return $this->coupon_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}