<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Shipping\Http\Requests\Api\Store;
use Modules\Shipping\Http\Requests\Api\Update;
use Modules\Shipping\Http\Resources\ShippingResource;
use Modules\Shipping\Service\ShippingService;
use ReflectionException;

class ShippingController extends CoreController
{
    private ShippingService $shipping_service;

    public function __construct(ShippingService $shipping_service)
    {
        $this->shipping_service = $shipping_service;
        $this->middleware('permission:shipping-list', ['only' => ['index']]);
        $this->middleware('permission:shipping-show', ['only' => ['show']]);
        $this->middleware('permission:shipping-create', ['only' => ['store']]);
        $this->middleware('permission:shipping-edit', ['only' => ['update']]);
        $this->middleware('permission:shipping-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return ShippingResource::collection($this->shipping_service->getAll());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
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
            ->respond(new ShippingResource($this->shipping_service->create($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
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
            ->respond(new ShippingResource($this->shipping_service->findById($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
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
            ->respond(new ShippingResource($this->shipping_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->shipping_service->delete($id);

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
