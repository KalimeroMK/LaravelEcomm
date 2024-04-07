<?php

namespace Modules\Order\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function index()
    {
        $orders = auth()->user()->hasRole('super-admin')
            ? $this->order_service->getAll()
            : $this->order_service->findByAllUser();

        return view('order::index', ['orders' => $orders]);
    }

    public function create()
    {
        return view('order::create', ['order' => new Order()]);
    }

    public function store(Store $request)
    {
        $this->order_service->store($request->all(), auth()->user());
        session()->forget(['cart', 'coupon']);

        return redirect()->route('home');
    }

    public function update(Update $request, Order $order)
    {
        $this->order_service->update($request->all(), $order);

        return redirect()->route('orders.index');
    }

    public function show(Order $order)
    {
        return view('order::show', ['order' => $order]);
    }

    public function edit(Order $order)
    {
        return view('order::edit', ['order' => $this->order_service->edit($order->id)]);
    }

    public function destroy(Order $order)
    {
        $this->order_service->destroy($order->id);

        return redirect()->back();
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function pdf($id): Response
    {
        $order = Order::getAllOrder($id);
        $file_name = $order->order_number.'-'.$order->first_name.'.pdf';
        $pdf = PDF::loadview('order::pdf', compact('order'));

        return $pdf->download($file_name);
    }

    // Income chart
    public function incomeChart(Request $request)
    {
        $year = Carbon::now()->year;
        // dd($year);
        $items = Order::with(['cart_info'])->whereYear('created_at', $year)->where('status', 'delivered')->get()
            ->groupBy(function ($d) {
                return Carbon::parse($d->created_at)->format('m');
            });
        // dd($items);
        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');
                // dd($amount);
                $m = intval($month);
                // return $m;
                isset($result[$m]) ? $result[$m] += $amount : $result[$m] = $amount;
            }
        }
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = (!empty($result[$i])) ? number_format((float) ($result[$i]), 2, '.', '') : 0.0;
        }

        return $data;
    }
}
