<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Actions\DeleteOrderAction;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Actions\ShowOrderAction;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\Actions\UpdateOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Http\Requests\Api\Search;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;

class OrderController extends CoreController
{
    public function __construct(
        private readonly GetAllOrdersAction $getAllAction,
        private readonly FindOrdersByUserAction $findByUserAction,
        private readonly ShowOrderAction $showAction,
        private readonly DeleteOrderAction $deleteAction,
        private readonly StoreOrderAction $storeAction,
        private readonly UpdateOrderAction $updateAction
    ) {
        // Permissions removed – policy-based authorization
    }

    public function index(Search $request): ResourceCollection
    {
        $this->authorize('viewAny', Order::class);
        $orders = auth()->user()->hasRole('super-admin')
            ? $this->getAllAction->execute()->orders
            : $this->findByUserAction->execute($request['user_id'])->orders;

        return OrderResource::collection($orders);
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $dto = OrderDTO::fromRequest($request);
        $order = $this->storeAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Order']))
            ->respond(new OrderResource($order));
    }

    /**
     * @throws Exception
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->showAction->execute($id);
        $this->authorize('view', $order);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Order']))
            ->respond(new OrderResource($order));
    }

    /**
     * @throws Exception
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(OrderRepository::class, 'update', $id);

        $dto = OrderDTO::fromRequest($request, $id);
        $order = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Order']))
            ->respond(new OrderResource($order));
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): JsonResponse
    {
        $order = $this->showAction->execute($id);
        $this->authorize('delete', $order);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Order']))
            ->respond(null);
    }
}
