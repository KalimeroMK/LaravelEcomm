<?php

namespace Modules\Shipping\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Shipping\Http\Requests\Api\Store;
use Modules\Shipping\Http\Requests\Api\Update;
use Modules\Shipping\Http\Resources\ShippingResource;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Service\ShippingService;

class ShippingController extends CoreController
{
    
    private ShippingService $shipping_service;
    
    public function __construct(ShippingService $shipping_service)
    {
        $this->shipping_service = $shipping_service;
        $this->authorizeResource(Shipping::class);
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return ShippingResource::collection($this->shipping_service->getAll());
    }
    
    /**
     *
     * @return mixed
     * @throws Exception
     */
    public function store(Store $request)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->shipping_service->shipping_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new ShippingResource($this->shipping_service->store($request->validated())));
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
                                $this->shipping_service->shipping_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new ShippingResource($this->shipping_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    public function update(Update $request, $id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.updateSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->shipping_service->shipping_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new ShippingResource($this->shipping_service->update($id, $request->validated())));
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
                                $this->shipping_service->shipping_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->shipping_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
