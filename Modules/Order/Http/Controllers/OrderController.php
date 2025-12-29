<?php

declare(strict_types=1);

namespace Modules\Order\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Actions\DeleteOrderAction;
use Modules\Order\Actions\FindOrdersByUserAction;
use Modules\Order\Actions\GenerateOrderPdfAction;
use Modules\Order\Actions\GetAllOrdersAction;
use Modules\Order\Actions\GetIncomeChartAction;
use Modules\Order\Actions\ShowOrderAction;
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
        private readonly ShowOrderAction $showAction,
        private readonly DeleteOrderAction $deleteAction,
        private readonly StoreOrderAction $storeAction,
        private readonly UpdateOrderAction $updateAction,
        private readonly GenerateOrderPdfAction $generatePdfAction,
        private readonly GetIncomeChartAction $getIncomeChartAction
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
        return view('order::create', ['order' => new Order]);
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

        // Update tracking if provided
        if ($request->has('tracking_number')) {
            $order->update([
                'tracking_number' => $request->input('tracking_number'),
                'tracking_carrier' => $request->input('tracking_carrier'),
                'shipped_at' => $request->input('tracking_number') ? now() : null,
            ]);
        }

        return redirect()->route('orders.index');
    }

    public function show(Order $order): View
    {
        $order = $this->showAction->execute($order->id);

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
        $order = Order::findOrFail($id);
        $this->authorize('view', $order);

        $pdf = $this->generatePdfAction->execute($id);
        $file_name = $order->order_number.'-'.($order->user->name ?? $order->first_name ?? 'order').'.pdf';

        return $pdf->download($file_name);
    }

    public function incomeChart(): array
    {
        return $this->getIncomeChartAction->execute();
    }
}
