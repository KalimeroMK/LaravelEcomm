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

        return view(theme_view('pages.my-orders'), ['orders' => $orders]);
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

        return view(theme_view('pages.order-detail'), ['order' => $order]);
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

        return view(theme_view('pages.order-track'), ['order' => $order]);
    }

    /**
     * Customer requests a return/refund for one of their orders.
     */
    public function requestReturn(\Illuminate\Http\Request $request, Order $order): \Illuminate\Http\RedirectResponse
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $request->validate(['reason' => 'required|string|min:10|max:2000']);

        if (! $order->canRequestReturn()) {
            return back()->with('error', __('A return can only be requested for shipped or delivered orders, once.'));
        }

        $orderReturn = \Modules\Order\Models\OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'reason' => $request->input('reason'),
            'status' => \Modules\Order\Models\OrderReturn::STATUS_REQUESTED,
        ]);

        // Confirmation to the customer + heads-up for the admins.
        $order->notifyCustomer(new \Modules\Order\Notifications\OrderReturnStatusNotification($orderReturn));

        $admins = \Modules\User\Models\User::role('super-admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \Modules\Core\Notifications\StatusNotification([
            'title' => 'Return requested for order '.$order->order_number,
            'actionURL' => route('orders.show', $order->id),
            'fas' => 'fa-undo',
        ]));

        return back()->with('success', __('Your return request was submitted. We will get back to you shortly.'));
    }
}
