<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Http\Requests\Api\Search;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Service\OrderService;
use ReflectionException;

class OrderController extends CoreController
{
    private OrderService $order_service;

    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
        $this->middleware('permission:order-list', ['only' => ['index']]);
        $this->middleware('permission:order-show', ['only' => ['show']]);
        $this->middleware('permission:order-create', ['only' => ['store']]);
        $this->middleware('permission:order-edit', ['only' => ['update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Search $request): ResourceCollection
    {
        return OrderResource::collection($this->order_service->search($request->validated()));
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
                            $this->order_service->order_repository->model
                        ),
                    ]
                )
            )
            ->respond(new OrderResource($this->order_service->store($request->all())));
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
                            $this->order_service->order_repository->model
                        ),
                    ]
                )
            )
            ->respond(new OrderResource($this->order_service->findById($id)));
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
                            $this->order_service->order_repository->model
                        ),
                    ]
                )
            )
            ->respond(new OrderResource($this->order_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->order_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->order_service->order_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
