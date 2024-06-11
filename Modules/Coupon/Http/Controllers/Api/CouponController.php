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
use ReflectionException;

class CouponController extends CoreController
{
    private CouponService $coupon_service;

    public function __construct(CouponService $coupon_service)
    {
        $this->coupon_service = $coupon_service;
    }

    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return CouponResource::collection($this->coupon_service->getAll());
    }

    /**
     * @param Store $request
     * @return JsonResponse
     * @throws ReflectionException
     */
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
            ->respond(new CouponResource($this->coupon_service->create($request->validated())));
    }

    /**
     * @param Coupon $coupon
     * @return JsonResponse
     * @throws ReflectionException
     * q*/
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
            ->respond(new CouponResource($this->coupon_service->findById($coupon->id)));
    }

    /**
     * @param Update $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Update $request, int $id)
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
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->coupon_service->delete($id);
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
            ->respond(null);
    }
}
