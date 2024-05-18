<?php

namespace Modules\Order\Service;

use Auth;
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
    public OrderRepository $order_repository;

    public function __construct(OrderRepository $order_repository)
    {
        $this->order_repository = $order_repository;
    }

    public function edit(int $id): ?Order
    {
        return $this->order_repository->findById($id);
    }

    public function show(int $id): ?Order
    {
        return $this->order_repository->findById($id);
    }

    public function destroy(int $id): void
    {
        $this->order_repository->delete($id);
    }

    public function getAll(): object
    {
        return $this->order_repository->findAll();
    }

    /**
     * Search orders based on given data.
     *
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function search(array $data): mixed
    {
        return $this->order_repository->search($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array<string, mixed>  $data
     * @return int
     */
    public function store(array $data): int
    {
        $order_data = [
            'order_number' => 'ORD-'.strtoupper(Str::random(10)),
            'user_id' => Auth::user()->id,
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

        $order->total_amount = Helper::totalCartPrice() + $shippingPrice - ($order_data['coupon'] ?? 0);

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
     * Update cart with order ID.
     *
     * @param  Order  $order
     * @return void
     */
    private function updateCartWithOrderId(Order $order): void
    {
        Cart::whereUserId(Auth::id())->whereOrderId(null)->update(['order_id' => $order->id]);
    }

    /**
     * Update the specified order.
     *
     * @param  array<string, mixed>  $data
     * @param  int  $id
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $order = $this->order_repository->findById($id);

        if ($data['status'] == 'delivered') {
            foreach ($order->cart as $cart) {
                $product = $cart->product;
                $product->stock -= $cart->quantity;
                $product->save();
            }
        }

        return $this->order_repository->update($order->id, $data);
    }

    /**
     * Find all orders by user.
     *
     * @return mixed
     */
    public function findByAllUser(): mixed
    {
        return $this->order_repository->findAllByUser();
    }
}
