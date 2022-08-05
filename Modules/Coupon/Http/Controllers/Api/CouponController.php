<?php

namespace Modules\Coupon\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Coupon\Http\Requests\Api\Store;
use Modules\Coupon\Http\Requests\Api\Update;
use Modules\Coupon\Service\CouponService;

class CouponController extends Controller
{
    private CouponService $coupon_service;
    
    public function __construct(CouponService $coupon_service)
    {
        $this->coupon_service = $coupon_service;
    }
    
    /**
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->sendResponse([$this->coupon_service->getAll()], 200);
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse
     */
    public function store(Store $request): JsonResponse
    {
        return $this->sendResponse([$this->coupon_service->store($request->all())], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        return $this->sendResponse([$this->coupon_service->edit($id)], 200);
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        dd($id, $request->all());
        
        return $this->sendResponse([$this->coupon_service->update($id, $request->all())], 200);
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        return $this->sendResponse([$this->coupon_service->destroy($id)], 200);
    }
}
