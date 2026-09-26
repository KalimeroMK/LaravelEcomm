<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Front;

use Illuminate\Support\Facades\Notification;
use Modules\Cart\Models\Cart;
use Modules\Front\Actions\ProcessCheckoutAction;
use Modules\Order\Models\Order;
use Modules\Order\Notifications\OrderPlacedNotification;
use Modules\Order\Notifications\OrderStatusUpdatedNotification;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CheckoutStockAndEmailTest extends ActionTestCase
{
    private function checkoutRequest(User $user): \Modules\Order\Http\Requests\Store
    {
        $request = \Modules\Order\Http\Requests\Store::create('/checkout', 'POST', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'phone' => '555-1234',
            'country' => 'US',
            'city' => 'New York',
            'address1' => '123 Main St',
            'post_code' => '10001',
            'payment_method' => 'cod',
        ]);
        $request->setUserResolver(fn () => $user);

        return $request;
    }

    public function test_cod_checkout_decrements_stock_and_sends_confirmation(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['status' => 'active', 'price' => 50.00, 'stock' => 10]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 50.00,
            'quantity' => 3,
            'order_id' => null,
        ]);

        app(ProcessCheckoutAction::class)->execute($this->checkoutRequest($user));

        $this->assertEquals(7, $product->fresh()->stock);
        Notification::assertSentTo($user, OrderPlacedNotification::class);
    }

    public function test_checkout_rejects_quantity_above_stock(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create(['status' => 'active', 'price' => 50.00, 'stock' => 2]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => 50.00,
            'quantity' => 5,
            'order_id' => null,
        ]);

        $response = app(ProcessCheckoutAction::class)->execute($this->checkoutRequest($user));

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        // No order was created and stock is untouched.
        $this->assertEquals(0, Order::count());
        $this->assertEquals(2, $product->fresh()->stock);
    }

    public function test_cancelling_order_restores_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['status' => 'active', 'stock' => 5]);
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'processing']);

        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'price' => $product->price,
            'quantity' => 2,
            'order_id' => $order->id,
        ]);

        $order->decrementStock();
        $this->assertEquals(3, $product->fresh()->stock);

        $order->restoreStock();
        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_status_notification_reaches_guest_email(): void
    {
        Notification::fake();

        $order = Order::factory()->create([
            'user_id' => null,
            'email' => 'guest@example.com',
            'status' => 'shipped',
            'tracking_number' => 'TRACK-1',
        ]);

        $order->notifyCustomer(new OrderStatusUpdatedNotification($order));

        Notification::assertSentOnDemand(
            OrderStatusUpdatedNotification::class,
            fn ($notification, $channels, $notifiable) => $notifiable->routes['mail'] === 'guest@example.com'
        );
    }
}
