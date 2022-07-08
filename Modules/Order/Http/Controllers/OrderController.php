<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Order\Http\Requests\Store;
use Modules\Order\Http\Requests\Update;
use Modules\Order\Models\Order;
use Modules\Order\Service\OrderService;

class OrderController extends Controller
{
    private OrderService $order_service;
    
    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return $this->order_service->index();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        return $this->order_service->store($request);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  int  $id
     *
     * @return RedirectResponse
     */
    public function update(Update $request, int $id): RedirectResponse
    {
        return $this->order_service->update($request, $id);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  Order  $order
     *
     * @return Application|Factory|View
     */
    public function show(Order $order): View|Factory|Application
    {
        return $this->order_service->show($order);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Application|Factory|View
     */
    public function edit(int $id): View|Factory|Application
    {
        return $this->order_service->edit($id);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        return $this->order_service->destroy($id);
    }
}
