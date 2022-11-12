<?php

namespace Modules\Order\Service;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Core\Helpers\Helper;
use Modules\Core\Notifications\StatusNotification;
use Modules\Order\Exceptions\SearchException;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;

class OrderService
{
    public OrderRepository $order_repository;
    
    public function __construct(OrderRepository $order_repository)
    {
        $this->order_repository = $order_repository;
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function edit($id): mixed
    {
        try {
            return $this->order_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
        try {
            return $this->order_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
        try {
            $this->order_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     * @throws SearchException
     */
    public function getAll($data): mixed
    {
        try {
            return $this->order_repository->search($data);
        } catch (Exception $exception) {
            throw new SearchException($exception);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param $data
     *
     * @return string
     */
    public function store($data): string
    {
        try {
            $order                      = new Order();
            $order_data                 = $data;
            $order_data['order_number'] = 'ORD-' . strtoupper(Str::random(10));
            $order_data['user_id']      = Auth::id();
            $order_data['shipping_id']  = $data->shipping;
            $shipping                   = Shipping::whereId($order_data['shipping_id'])->pluck('price');
            $order_data['sub_total']    = Helper::totalCartPrice();
            $order_data['quantity']     = Helper::cartCount();
            if (session('coupon')) {
                $order_data['coupon'] = session('coupon')['value'];
            }
            if ($data->shipping) {
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
            Cart::whereUserId(Auth()->id())->whereOrderId(null)->update(['order_id' => $order->id]);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $data
     * @param $id
     *
     * @return mixed
     */
    public function update($data, $id): mixed
    {
        try {
            $order = $this->order_repository->findById($id);
            
            if ($data->status == 'delivered') {
                foreach ($order->cart as $cart) {
                    $product        = $cart->product;
                    $product->stock -= $cart->quantity;
                    $product->save();
                }
            }
            
            return $this->order_repository->update($id, $data);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed
     */
    public function findByAllUser(): mixed
    {
        try {
            return $this->order_repository->findAllByUser();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}