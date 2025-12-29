<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\ShowOrderAction;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;

class UserOrderController extends CoreController
{
    public function __construct(
        private readonly FindOrdersByUserAction $findByUserAction,
        private readonly ShowOrderAction $showAction
    ) {}

    /**
     * Display user's order history.
     */
    public function history(): ResourceCollection
    {
        $orders = $this->findByUserAction->execute(auth()->id());

        return OrderResource::collection($orders);
    }

    /**
     * Display order details for authenticated user.
     */
    public function detail(int $id): JsonResponse
    {
        $order = $this->showAction->execute($id);
        $order->load(['user', 'carts.product', 'shipping']);

        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        return $this
            ->setMessage('Order details retrieved successfully.')
            ->respond(new OrderResource($order));
    }

    /**
     * Display order tracking information.
     */
    public function track(int $id): JsonResponse
    {
        $order = $this->showAction->execute($id);
        $order->load(['user', 'carts.product', 'shipping']);

        // Ensure user can only track their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        return $this
            ->setMessage('Order tracking information retrieved successfully.')
            ->respond([
                'order' => new OrderResource($order),
                'tracking' => [
                    'tracking_number' => $order->tracking_number,
                    'tracking_carrier' => $order->tracking_carrier,
                    'shipped_at' => $order->shipped_at,
                    'status' => $order->status,
                ],
            ]);
    }
}
