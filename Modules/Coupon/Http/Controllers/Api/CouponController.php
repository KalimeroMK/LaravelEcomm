<?php

namespace Modules\Coupon\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;
use Modules\Coupon\Http\Requests\Api\Store;
use Modules\Coupon\Http\Requests\Api\Update;
use Modules\Coupon\Http\Resource\CouponResource;
use Modules\Coupon\Service\CouponService;

class CouponController extends Controller
{
    use ApiResponses;
    
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
        return BannerResource::collection($this->coupon_service->getAll());
    }
    
    public function store(Store $request)
    {
        try {
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
                ->respond(new BannerResource($this->coupon_service->store($request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function show($id)
    {
        try {
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
                ->respond(new CouponResource($this->coupon_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return string
     */
    public function update(Update $request, $id)
    {
        try {
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
                ->respond(new BannerResource($this->coupon_service->update($id, $request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function destroy($id)
    {
        try {
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
                ->respond($this->coupon_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
