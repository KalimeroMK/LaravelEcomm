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
use Modules\Order\Actions\DeleteOrderAction;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\Actions\UpdateOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Http\Requests\Api\Store;
use Modules\Order\Http\Requests\Api\Update;
use Modules\Order\Models\Order;

class OrderController extends CoreController
{
    public function __construct(
        private readonly GetAllOrdersAction $getAllAction,
        private readonly FindOrdersByUserAction $findByUserAction,
        private readonly DeleteOrderAction $deleteAction,
        private readonly StoreOrderAction $storeAction,
        private readonly UpdateOrderAction $updateAction
    ) {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index(): View|Factory|Application
    {
        $orders = auth()->user()->hasRole('super-admin')
            ? $this->getAllAction->execute()
            : $this->findByUserAction->execute(auth()->id());

        return view('order::index', ['orders' => $orders]);
    }

    public function create(): View
    {
        return view('order::create', ['order' => new Order()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $dto = OrderDTO::fromRequest($request);
        $this->storeAction->execute($dto);

        session()->forget(['cart', 'coupon']);

        return redirect()->route('home');
    }

    public function update(Update $request, Order $order): RedirectResponse
    {
        $dto = OrderDTO::fromRequest($request, $order->id);
        $this->updateAction->execute($dto);

        return redirect()->route('orders.index');
    }

    public function show(Order $order): View
    {
        return view('order::show', ['order' => $order]);
    }

    public function edit(Order $order): View
    {
        return view('order::edit', ['order' => $order]);
    }

    public function destroy(Order $order): RedirectResponse
    {
        $this->deleteAction->execute($order->id);

        return redirect()->back();
    }

    public function pdf(int $id): Response
    {
        $order = Order::findOrFail($id); // optional: authorize('view', $order)

        $file_name = $order->order_number.'-'.$order->first_name.'.pdf';
        $pdf = Pdf::loadView('order::pdf', ['order' => $order]);

        return $pdf->download($file_name);
    }

    public function incomeChart(): array
    {
        $year = Carbon::now()->year;

        $items = Order::with(['cart_info'])
            ->whereYear('created_at', $year)
            ->where('status', 'delivered')
            ->get()
            ->groupBy(fn ($d) => Carbon::parse($d->created_at)->format('m'));

        $result = [];

        foreach ($items as $month => $orderGroup) {
            foreach ($orderGroup as $order) {
                $amount = $order->cart_info->sum('amount');
                $m = (int) $month;
                $result[$m] = ($result[$m] ?? 0) + $amount;
            }
        }

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $timestamp = mktime(0, 0, 0, $i, 1);
            $monthName = $timestamp === false ? 'Invalid' : date('F', $timestamp);
            $data[$monthName] = isset($result[$i]) ? (float) number_format($result[$i], 2, '.', '') : 0.0;
        }

        return $data;
    }
}
