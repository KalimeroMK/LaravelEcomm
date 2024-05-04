<?php

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
use Modules\Order\Models\Order;
use Modules\Order\Service\OrderService;
use ReflectionException;

class OrderController extends CoreController
{

    private OrderService $order_service;

    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * @param  Search  $request
     *
     * @return ResourceCollection
     */
    public function index(Search $request): ResourceCollection
    {
        return OrderResource::collection($this->order_service->search($request->validated()));
    }

    /**
     *
     * @return mixed
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
                            $this->order_service->order_repository->model
                        ),
                    ]
                )
            )
            ->respond(new OrderResource($this->order_service->store($request->validated())));
    }

    /**
     * @param  Order  $order
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function show(Order $order)
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
            ->respond(new OrderResource($this->order_service->show($order->id)));
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
     * @param  Order  $order
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function destroy(Order $order)
    {
        $this->order_service->destroy($order->id);
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
