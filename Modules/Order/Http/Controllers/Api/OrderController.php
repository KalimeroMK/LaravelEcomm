<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Actions\DeleteOrderAction;
use Modules\Order\Actions\SearchOrdersAction;
use Modules\Order\Actions\ShowOrderAction;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\Actions\UpdateOrderAction;
use Modules\Order\Http\Requests\Api\Search;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Http\Resources\OrderResource;

class OrderController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:order-list', ['only' => ['index']]);
        $this->middleware('permission:order-show', ['only' => ['show']]);
        $this->middleware('permission:order-create', ['only' => ['store']]);
        $this->middleware('permission:order-edit', ['only' => ['update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }

    public function index(Search $request): ResourceCollection
    {
        $ordersDto = (new SearchOrdersAction())->execute($request->validated());

        return OrderResource::collection($ordersDto->orders);
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $orderDto = (new StoreOrderAction())->execute($request->all());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Order']))->respond(new OrderResource($orderDto));
    }

    /**
     * @throws Exception
     */
    public function show(int $id): JsonResponse
    {
        $orderDto = (new ShowOrderAction())->execute($id);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Order']))->respond(new OrderResource($orderDto));
    }

    /**
     * @throws Exception
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $orderDto = (new UpdateOrderAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Order']))->respond(new OrderResource($orderDto));
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeleteOrderAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Order']))->respond(null);
    }
}
