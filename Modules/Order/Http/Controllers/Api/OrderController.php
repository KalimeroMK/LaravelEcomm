<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Actions\DeleteOrderAction;
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
        private readonly OrderRepository $repository,
        private readonly ShowOrderAction $showAction,
        private readonly StoreOrderAction $storeAction,
        private readonly UpdateOrderAction $updateAction,
        private readonly DeleteOrderAction $deleteAction
    ) {}

    public function index(Search $request): ResourceCollection
    {
        $this->authorize('viewAny', Order::class);

        $orders = auth()->user()->hasRole('super-admin')
            ? $this->repository->paginateAll()
            : $this->repository->findAllByUser($request['user_id']);

        return OrderResource::collection($orders);
    }

    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Order::class);

        $dto = OrderDTO::fromRequest($request);
        $order = $this->storeAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Order']))
            ->respond(new OrderResource($order));
    }

    public function show(int $id): JsonResponse
    {
        $order = $this->showAction->execute($id);
        $this->authorize('view', $order);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Order']))
            ->respond(new OrderResource($order));
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $existingOrder = $this->authorizeFromRepo(OrderRepository::class, 'update', $id);

        $dto = OrderDTO::fromRequest($request, $id, $existingOrder);

        $order = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Order']))
            ->respond(new OrderResource($order));
    }

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
