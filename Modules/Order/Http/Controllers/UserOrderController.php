<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\ShowOrderAction;
use Modules\Order\Models\Order;

class UserOrderController extends Controller
{
    public function __construct(
        private readonly FindOrdersByUserAction $findByUserAction,
        private readonly ShowOrderAction $showAction
    ) {
        $this->middleware('auth');
    }

    /**
     * Display user's order history
     */
    public function history(): View|Factory|Application
    {
        $orders = $this->findByUserAction->execute(Auth::id());

        return view('order::user.history', ['orders' => $orders]);
    }

    /**
     * Display order details for authenticated user
     */
    public function detail(Order $order): View|Factory|Application
    {
        // Ensure user can only view their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['user', 'carts.product', 'shipping']);

        return view('order::user.detail', ['order' => $order]);
    }

    /**
     * Display order tracking information
     */
    public function track(Order $order): View|Factory|Application
    {
        // Ensure user can only track their own orders
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $order->load(['user', 'carts.product', 'shipping']);

        return view('order::user.track', ['order' => $order]);
    }
}
