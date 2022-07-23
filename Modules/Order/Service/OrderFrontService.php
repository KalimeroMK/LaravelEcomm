<?php

namespace Modules\Order\Service;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Models\Cart;
use Modules\Order\Http\Controllers\OrderFrontController;
use Modules\Order\Models\Order;
use PDF;

class OrderFrontService
{
    private OrderFrontController $order_front_controller;
    
    public function __construct(OrderFrontController $order_front_controller)
    {
        $this->order_front_controller = $order_front_controller;
    }
    
    /**
     * @return array
     */
    public function incomeChart(): array
    {
        $year   = Carbon::now()->year;
        $items  = Order::with(['cart_info'])->whereYear('created_at', $year)->where('status', 'delivered')->get()
                       ->groupBy(function ($d) {
                           return Carbon::parse($d->created_at)->format('m');
                       });
        $result = [];
        foreach ($items as $month => $item_collections) {
            foreach ($item_collections as $item) {
                $amount = $item->cart_info->sum('amount');
                $m      = intval($month);
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
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->order_front_controller->payment->calculate($request);
        
        if (request('payment_method') == 'paypal') {
            return redirect()->route('payment')->with($data[0], $data[1]);
        } elseif (request('payment_method') == 'stripe') {
            return redirect()->route('stripe')->with($data);
        } else {
            session()->forget('cart');
            session()->forget('coupon');
        }
        Cart::where('user_id', Auth::id())->where('order_id', null)->update(['order_id' => $data[0]]);
        
        request()->session()->flash('success', 'Your product successfully placed in order');
        
        return redirect()->route('home');
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