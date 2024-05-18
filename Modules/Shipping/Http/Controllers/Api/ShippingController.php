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
use ReflectionException;

class ShippingController extends CoreController
{

    private ShippingService $shipping_service;

    public function __construct(ShippingService $shipping_service)
    {
        $this->shipping_service = $shipping_service;
        $this->authorizeResource(Shipping::class, 'shipping');
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
     * @return JsonResponse
     * @throws Exception
     */
    public function store(Store $request)
    {
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
    }

    /**
     * @param  Shipping  $shipping
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function show(Shipping $shipping)
    {
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
            ->respond(new ShippingResource($this->shipping_service->show($shipping->id)));
    }

    /**
     * @param  Update  $request
     * @param  Shipping  $shipping
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Update $request, Shipping $shipping): JsonResponse
    {
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
            ->respond(new ShippingResource($this->shipping_service->update($shipping->id, $request->validated())));
    }

    /**
     * @param  Shipping  $shipping
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function destroy(Shipping $shipping)
    {
        $this->shipping_service->destroy($shipping->id);
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
            ->respond(null);
    }
}
