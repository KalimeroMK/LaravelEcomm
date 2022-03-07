<?php

    namespace Modules\Order\Http\Controllers;

    use App\Helpers\Helper;
    use App\Http\Controllers\Controller;
    use App\Notifications\StatusNotification;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Validation\ValidationException;
    use Modules\Cart\Models\Cart;
    use Modules\Order\Http\Requests\Store;
    use Modules\Order\Models\Order;
    use Modules\Shipping\Models\Shipping;
    use Modules\User\Models\User;
    use Notification;

    class OrderController extends Controller
    {
        /**
         * Display a listing of the resource.
         *
         * @return Application|Factory|View
         */
        public function index()
        {
            $orders = Order::with('shipping')->orderBy('id', 'DESC')->paginate(10);

            return view('order::index', compact('orders'));
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
            if (empty(Cart::where('user_id', auth()->user()->id)->where('order_id', null)->first())) {
                request()->session()->flash('error', 'Cart is Empty !');

                return back();
            }

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
            // return $order_data['total_amount'];
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
            Cart::where('user_id', auth()->user()->id)->where('order_id', null)->update(['order_id' => $order->id]);

            request()->session()->flash('success', 'Your product successfully placed in order');

            return redirect()->route('home');
        }

        /**
         * Display the specified resource.
         *
         * @param  Order  $order
         *
         * @return Application|Factory|View
         */
        public function show(Order $order)
        {
            return view('order::show')->with('order', $order);
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  int  $id
         *
         * @return Application|Factory|View
         */
        public function edit(int $id)
        {
            $order = Order::findOrFail($id);

            return view('order::edit')->with('order', $order);
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Request  $request
         * @param  int  $id
         *
         * @return RedirectResponse
         * @throws ValidationException
         */
        public function update(Request $request, int $id): RedirectResponse
        {
            $order = Order::find($id);
            $this->validate($request, [
                'status' => 'required|in:new,process,delivered,cancel',
            ]);
            $data = $request->all();
            // return $request->status;
            if ($request->status == 'delivered') {
                foreach ($order->cart as $cart) {
                    $product = $cart->product;
                    // return $product;
                    $product->stock -= $cart->quantity;
                    $product->save();
                }
            }
            $status = $order->fill($data)->save();
            if ($status) {
                request()->session()->flash('success', 'Successfully updated order');
            } else {
                request()->session()->flash('error', 'Error while updating order');
            }

            return redirect()->route('orders.index');
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
            $order = Order::find($id);
            if ($order) {
                $status = $order->delete();
                if ($status) {
                    request()->session()->flash('success', 'Order Successfully deleted');
                } else {
                    request()->session()->flash('error', 'Order can not deleted');
                }

                return redirect()->route('orders.index');
            } else {
                request()->session()->flash('error', 'Order can not found');

                return redirect()->back();
            }
        }

    }