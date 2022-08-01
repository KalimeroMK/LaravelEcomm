<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Order\Http\Requests\Store;
use Modules\Order\Http\Requests\Update;
use Modules\Order\Models\Order;
use Modules\Order\Service\OrderService;
use PDF;

class OrderController extends Controller
{
    private OrderService $order_service;
    
    public function __construct(OrderService $order_service)
    {
        $this->order_service = $order_service;
        $this->middleware('permission:order-list');
        $this->middleware('permission:order-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:order-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:order-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        if (Auth::user()->hasRole('client')) {
            return view('order::index', ['orders' => $this->order_service->findByAllUser()]);
        } else {
            return view('order::index', ['orders' => $this->order_service->index()]);
        }
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
        $this->order_service->update($request, $id);
        
        return redirect()->route('orders.index');
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
        return view('order::show', ['order' => $this->order_service->show($order->id)]);
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
        return view('order::edit', ['order' => $this->order_service->edit($id)]);
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
        $this->order_service->destroy($id);
        
        return redirect()->back();
    }
    
    /**
     * @param  Request  $request
     *
     * @return Response
     */
    public function pdf(Request $request): Response
    {
        $order     = Order::getAllOrder($request->id);
        $file_name = $order->order_number.'-'.$order->first_name.'.pdf';
        $pdf       = PDF::loadview('backend.order.pdf', compact('order'));
        
        return $pdf->download($file_name);
    }
}
