<?php

namespace Modules\Order\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Models\Order;
use Modules\Order\Service\OrderService;

class OrderController extends CoreController
{
    private OrderService $order_service;

    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
        $this->authorizeResource(Order::class);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|Factory|\Illuminate\Contracts\View\View|Application|View
     */
    public function index()
    {
        $orders = auth()->user()->hasRole('super-admin')
            ? $this->order_service->getAll()
            : $this->order_service->findByAllUser();

        return view('order::index', ['orders' => $orders]);
    }

    public function create(): View
    {
        return view('order::create', ['order' => new Order()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->order_service->store($request->all());
        session()->forget(['cart', 'coupon']);

        return redirect()->route('home');
    }

    public function update(Update $request, Order $order): RedirectResponse
    {
        $this->order_service->update($order->id, $request->all());

        return redirect()->route('orders.index');
    }

    public function show(Order $order): View
    {
        return view('order::show', ['order' => $order]);
    }

    public function edit(Order $order): View
    {
        return view('order::edit', ['order' => $this->order_service->findById($order->id)]);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->order_service->delete($order->id);

        return redirect()->back();
    }

    /**
     * Generate PDF for a given order ID.
     *
     * @param int $id
     * @return Response
     */
    public function pdf(int $id): Response
    {
        $order = Order::findOrFail($id);  // Ensure we get a single order or fail

        $file_name = $order->order_number . '-' . $order->first_name . '.pdf';
        $pdf = PDF::loadview('order::pdf', compact('order'));

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
            ->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });

        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');
                $m = intval($month);
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = !empty($result[$i]) ? number_format((float)$result[$i], 2, '.', '') : 0.0;
        }

        return $data;
    }
}
