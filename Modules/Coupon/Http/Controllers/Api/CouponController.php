<?php

namespace Modules\Coupon\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Coupon\Http\Requests\Api\Store;
use Modules\Coupon\Http\Requests\Api\Update;
use Modules\Coupon\Http\Resource\CouponResource;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Service\CouponService;

class CouponController extends CoreController
{
    private CouponService $coupon_service;

    public function __construct(CouponService $coupon_service)
    {
        $this->coupon_service = $coupon_service;
        $this->authorizeResource(Coupon::class, 'coupon');
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return CouponResource::collection($this->coupon_service->getAll());
    }

    public function store(Store $request)
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->coupon_service->coupon_repository->model
                        ),
                    ]
                )
            )
            ->respond(new CouponResource($this->coupon_service->store($request->validated())));
    }

    /**
     * @param  Coupon  $coupon
     * @return JsonResponse
     */
    public function show(Coupon $coupon)
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(
                            $this->coupon_service->coupon_repository->model
                        ),
                    ]
                )
            )
            ->respond(new CouponResource($this->coupon_service->show($coupon->id)));
    }

    /**
     * @param  Update  $request
     * @param $id
     *
     * @return string
     */
    public function update(Update $request, $id)
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->coupon_service->coupon_repository->model
                        ),
                    ]
                )
            )
            ->respond(new CouponResource($this->coupon_service->update($id, $request->validated())));
    }

    /**
     * @param  Coupon  $coupon
     * @return JsonResponse
     */
    public function destroy(Coupon $coupon)
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->coupon_service->coupon_repository->model
                        ),
                    ]
                )
            )
            ->respond($this->coupon_service->destroy($coupon->id));
    }
}
