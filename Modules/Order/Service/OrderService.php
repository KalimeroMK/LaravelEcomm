<?php

namespace Modules\Order\Service;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Modules\Cart\Models\Cart;
use Modules\Core\Helpers\Helper;
use Modules\Core\Notifications\StatusNotification;
use Modules\Order\Models\Order;
use Modules\Order\Repository\OrderRepository;
use Modules\User\Models\User;

class OrderService
{
    protected OrderRepository $order_repository;

    public function __construct(OrderRepository $order_repository)
    {
        $this->order_repository = $order_repository;
    }

    public function edit($id)
    {
        return $this->order_repository->findById($id);
    }

    public function show($id)
    {
        return $this->order_repository->findById($id);
    }

    public function destroy($id)
    {
        $this->order_repository->delete($id);
    }

    public function getAll()
    {
        return $this->order_repository->findAll();
    }

    public function search($data)
    {
        return $this->order_repository->search($data);
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
        $order_data = [
            'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            'user_id' => $user->id,
            'shipping_id' => $data['shipping'],
            'sub_total' => Helper::totalCartPrice(),
            'quantity' => Helper::cartCount(),
            'status' => 'new',
        ];

        if (session('coupon')) {
            $order_data['coupon'] = session('coupon')['value'];
        }

        $order = new Order($order_data);

        $shippingPrice = $order->shipping->price ?? 0;

        if ($data->shipping) {
            $order->total_amount = Helper::totalCartPrice() + $shippingPrice - ($order_data['coupon'] ?? 0);
        } else {
            $order->total_amount = Helper::totalCartPrice() - ($order_data['coupon'] ?? 0);
        }

        $order->payment_method = (request('payment_method') == 'paypal') ? 'paypal' : 'cod';
        $order->payment_status = ($order->payment_method === 'paypal') ? 'paid' : 'unpaid';

        $order->save();

        $this->sendNewOrderNotification($order);

        $this->updateCartWithOrderId($order);

        return $order->id;
    }

    private function sendNewOrderNotification(Order $order): void
    {
        $details = [
            'title' => 'New order created',
            'actionURL' => route('order.show', $order->id),
            'fas' => 'fa-file-alt',
        ];

        $superAdmins = User::role('super-admin')->get();
        Notification::send($superAdmins, new StatusNotification($details));
    }

    /**
     * @param $order
     * @return void
     */
    private function updateCartWithOrderId($order): void
    {
        Cart::whereUserId(Auth()->id())->whereOrderId(null)->update(['order_id' => $order->id]);
    }


    public function update($data, Order $order)
    {
        $order = $this->order_repository->findById($order->id);

        if ($data->status == 'delivered') {
            foreach ($order->cart as $cart) {
                $product = $cart->product;
                $product->stock -= $cart->quantity;
                $product->save();
            }
        }

        return $this->order_repository->update($order->id, $data);
    }

    /**
     * @return mixed
     */
    public function findByAllUser(): mixed
    {
        return $this->order_repository->findAllByUser();
    }
}
