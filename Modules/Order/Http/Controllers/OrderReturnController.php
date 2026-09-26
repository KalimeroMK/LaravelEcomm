<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Order\Actions\RefundOrderReturnAction;
use Modules\Order\Models\OrderReturn;
use Modules\Order\Notifications\OrderReturnStatusNotification;

/**
 * Admin management of customer return/refund requests (RMA).
 */
class OrderReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|super-admin']);
    }

    public function index(): View
    {
        $returns = OrderReturn::with('order.user')
            ->orderByRaw("CASE status WHEN 'requested' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END")
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('order::returns.index', ['returns' => $returns]);
    }

    public function approve(Request $request, OrderReturn $orderReturn): RedirectResponse
    {
        $request->validate(['admin_note' => 'nullable|string|max:2000']);

        $orderReturn->update([
            'status' => OrderReturn::STATUS_APPROVED,
            'admin_note' => $request->input('admin_note') ?? $orderReturn->admin_note,
        ]);

        $orderReturn->order->notifyCustomer(new OrderReturnStatusNotification($orderReturn));

        return back()->with('success', 'Return approved - the customer has been notified.');
    }

    public function reject(Request $request, OrderReturn $orderReturn): RedirectResponse
    {
        $request->validate(['admin_note' => 'nullable|string|max:2000']);

        $orderReturn->update([
            'status' => OrderReturn::STATUS_REJECTED,
            'admin_note' => $request->input('admin_note') ?? $orderReturn->admin_note,
        ]);

        $orderReturn->order->notifyCustomer(new OrderReturnStatusNotification($orderReturn));

        return back()->with('success', 'Return rejected - the customer has been notified.');
    }

    public function refund(Request $request, OrderReturn $orderReturn, RefundOrderReturnAction $refundAction): RedirectResponse
    {
        $request->validate([
            'refund_amount' => 'nullable|numeric|min:0.01',
            'admin_note' => 'nullable|string|max:2000',
        ]);

        if ($orderReturn->status === OrderReturn::STATUS_REFUNDED) {
            return back()->with('error', 'This return is already refunded.');
        }

        $result = $refundAction->execute(
            $orderReturn,
            $request->filled('refund_amount') ? (float) $request->input('refund_amount') : null,
            $request->input('admin_note')
        );

        $message = $result['gateway_refunded']
            ? 'Refund issued through the payment gateway; stock restored.'
            : 'Refund recorded (manual payout required for this payment method); stock restored.';

        return back()->with('success', $message);
    }
}
