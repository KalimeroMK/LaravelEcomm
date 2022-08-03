<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
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
     * @return Application|Factory|View
     */
    public function create(): Application|Factory|View
    {
        return view('order::create', ['order' => new Order()]);
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
     * @param $id
     *
     * @return Response
     */
    public function pdf($id): Response
    {
        $order     = Order::getAllOrder($id);
        $file_name = $order->order_number.'-'.$order->first_name.'.pdf';
        $pdf       = PDF::loadview('order::pdf', compact('order'));
        
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
            $monthName        = date('F', mktime(0, 0, 0, $i, 1));
            $data[$monthName] = ( ! empty($result[$i])) ? number_format((float)($result[$i]), 2, '.', '') : 0.0;
        }
        
        return $data;
    }
}
