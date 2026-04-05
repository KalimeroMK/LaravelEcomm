<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Actions\DeleteOrderAction;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\GenerateOrderPdfAction;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Actions\GetIncomeChartAction;
use Modules\Order\Actions\ReorderAction;
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
        private readonly GetAllOrdersAction $getAllAction,
        private readonly FindOrdersByUserAction $findByUserAction,
        private readonly ShowOrderAction $showAction,
        private readonly StoreOrderAction $storeAction,
        private readonly UpdateOrderAction $updateAction,
        private readonly DeleteOrderAction $deleteAction,
        private readonly GenerateOrderPdfAction $generatePdfAction,
        private readonly GetIncomeChartAction $getIncomeChartAction,
        private readonly ReorderAction $reorderAction,
    ) {}

    public function index(Search $request): ResourceCollection
    {
        $this->authorize('viewAny', Order::class);

        $orders = $request->user()?->hasRole('super-admin')
            ? $this->getAllAction->execute()
            : $this->findByUserAction->execute($request['user_id'] ?? $request->user()?->getAuthIdentifier());

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

    /**
     * Generate PDF for order.
     */
    public function pdf(int $id): Response
    {
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);

        $pdf = $this->generatePdfAction->execute($id);
        $file_name = preg_replace('/[^a-zA-Z0-9\-_]/', '', $order->order_number ?? 'order').'.pdf';

        return $pdf->download($file_name);
    }

    /**
     * Get income chart data.
     */
    public function incomeChart(): JsonResponse
    {
        $this->authorize('viewAny', Order::class);

        $data = $this->getIncomeChartAction->execute();

        return $this
            ->setMessage('Income chart data retrieved successfully.')
            ->respond($data);
    }

    /**
     * Reorder a previous order.
     */
    public function reorder(int $id): JsonResponse
    {
        $order = $this->showAction->execute($id);
        $this->authorize('view', $order);

        // Ensure user can only reorder their own orders
        $userId = request()->user()?->getAuthIdentifier();
        if ($order->user_id !== $userId) {
            abort(403, 'Unauthorized access to this order.');
        }

        $result = $this->reorderAction->execute($id, $userId);

        if ($result['success']) {
            return $this
                ->setMessage($result['message'])
                ->respond([
                    'added_items' => $result['added_items'],
                    'skipped_items' => $result['skipped_items'],
                ]);
        }

        return $this
            ->setMessage($result['message'])
            ->setStatusCode(422)
            ->respond(null);
    }
}
