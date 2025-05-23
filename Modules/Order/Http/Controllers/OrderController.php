<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\Actions\UpdateOrderAction;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Models\Order;

class OrderController extends CoreController
{
    public function __construct() {}

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|View
     */
    public function index()
    {
        $ordersDto = auth()->user()->hasRole('super-admin')
            ? (new GetAllOrdersAction())->execute()
            : (new FindOrdersByUserAction())->execute(auth()->id());

        return view('order::index', ['orders' => $ordersDto->orders]);
    }

    public function create(): View
    {
        return view('order::create', ['order' => new Order]);
    }

    public function store(Store $request): RedirectResponse
    {
        (new StoreOrderAction())->execute($request->all());
        session()->forget(['cart', 'coupon']);

        return redirect()->route('home');
    }

    public function update(Update $request, Order $order): RedirectResponse
    {
        (new UpdateOrderAction())->execute($order->id, $request->all());

        return redirect()->route('orders.index');
    }

    public function show(Order $order): View
    {
        return view('order::show', ['order' => $order]);
    }

    public function edit(Order $order): View
    {
        // For edit, just pass the Order model directly or wrap in DTO if needed
        return view('order::edit', ['order' => $order]);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->back();
    }

    /**
     * Generate PDF for a given order ID.
     */
    public function pdf(int $id): Response
    {
        $order = Order::findOrFail($id);  // Ensure we get a single order or fail

        $file_name = $order->order_number.'-'.$order->first_name.'.pdf';
        $pdf = Pdf::loadview('order::pdf', ['order' => $order]);

        return $pdf->download($file_name);
    }

    /**
     * Generate income chart data for the current year.
     *
     * @return array<string, float>
     */
    public function incomeChart(): array
    {
        $year = Carbon::now()->year;
        $items = Order::with(['cart_info'])
            ->whereYear('created_at', $year)
            ->where('status', 'delivered')
            ->get()
            ->groupBy(function ($d): string {
                return Carbon::parse($d->created_at)->format('m');
            });

        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');
                $m = (int) $month;
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1);
            $monthName = $timestamp === false ? 'Invalid' : date('F', $timestamp);
            $data[$monthName] = empty($result[$i]) ? 0.0 : (float) number_format($result[$i], 2, '.', '');
        }

        return $data;
    }
}
