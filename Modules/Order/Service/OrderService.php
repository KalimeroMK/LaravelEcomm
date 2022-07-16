<?php

namespace Modules\Order\Service;

use App\Helpers\Helper;
use App\Notifications\StatusNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Order\Http\Requests\Store;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

class OrderService
{
    private OrderRepository $order_repository;
    
    public function __construct(OrderRepository $order_repository)
    {
        $this->order_repository = $order_repository;
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
        $order                      = new Order();
        $order_data                 = $request->all();
        $order_data['order_number'] = 'ORD-'.strtoupper(Str::random(10));
        $order_data['user_id']      = $request->user()->id;
        $order_data['shipping_id']  = $request->shipping;
        $shipping                   = Shipping::where('id', $order_data['shipping_id'])->pluck('price');
        $order_data['sub_total']    = Helper::totalCartPrice();
        $order_data['quantity']     = Helper::cartCount();
        if (session('coupon')) {
            $order_data['coupon'] = session('coupon')['value'];
        }
        if ($request->shipping) {
            if (session('coupon')) {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0] - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice() + $shipping[0];
            }
        } else {
            if (session('coupon')) {
                $order_data['total_amount'] = Helper::totalCartPrice() - session('coupon')['value'];
            } else {
                $order_data['total_amount'] = Helper::totalCartPrice();
            }
        }
        $order_data['status'] = "new";
        if (request('payment_method') == 'paypal') {
            $order_data['payment_method'] = 'paypal';
            $order_data['payment_status'] = 'paid';
        } else {
            $order_data['payment_method'] = 'cod';
            $order_data['payment_status'] = 'Unpaid';
        }
        $order->fill($order_data);
        $order->save();
        
        $details = [
            'title'     => 'New order created',
            'actionURL' => route('order.show', $order->id),
            'fas'       => 'fa-file-alt',
        ];
        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
        if (request('payment_method') == 'paypal') {
            return redirect()->route('payment')->with(['id' => $order->id]);
        } else {
            session()->forget('cart');
            session()->forget('coupon');
        }
        Cart::whereUserId(Auth()->id())->whereOrderId(null)->update(['order_id' => $order->id]);
        
        return redirect()->route('home');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * =     */
    public function edit(int $id)
    {
        return $this->order_repository->findById($id);
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        return $this->order_repository->findById($id);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @param  int  $id
     *
     * @return void
     */
    public function update($request, int $id): void
    {
        $order = $this->order_repository->findById($id);
        
        if ($request->status == 'delivered') {
            foreach ($order->cart as $cart) {
                $product        = $cart->product;
                $product->stock -= $cart->quantity;
                $product->save();
            }
        }
        $order->fill($request)->save();
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->order_repository->delete($id);
    }
    
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->order_repository->findAll();
    }
}