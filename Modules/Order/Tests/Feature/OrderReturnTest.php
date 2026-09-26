<?php

declare(strict_types=1);

namespace Modules\Order\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderReturn;
use Modules\Order\Notifications\OrderReturnStatusNotification;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class OrderReturnTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_customer_can_request_return_for_delivered_order(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);

        $response = $this->actingAs($user)->post(route('user.orders.return.request', $order), [
            'reason' => 'The product arrived damaged, I would like a refund.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('order_returns', [
            'order_id' => $order->id,
            'user_id' => $user->id,
            'status' => OrderReturn::STATUS_REQUESTED,
        ]);
        Notification::assertSentTo($user, OrderReturnStatusNotification::class);
    }

    public function test_return_cannot_be_requested_twice_or_for_pending_orders(): void
    {
        $user = User::factory()->create();

        $pending = Order::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $this->actingAs($user)
            ->post(route('user.orders.return.request', $pending), ['reason' => 'Changed my mind about it all.'])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('order_returns', 0);

        $delivered = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        OrderReturn::create([
            'order_id' => $delivered->id,
            'user_id' => $user->id,
            'reason' => 'First request for this order.',
        ]);
        $this->actingAs($user)
            ->post(route('user.orders.return.request', $delivered), ['reason' => 'Second request for this order.'])
            ->assertSessionHas('error');
        $this->assertDatabaseCount('order_returns', 1);
    }

    public function test_customer_cannot_request_return_on_someone_elses_order(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $owner->id, 'status' => 'delivered']);

        $this->actingAs($other)
            ->post(route('user.orders.return.request', $order), ['reason' => 'Not my order but trying anyway.'])
            ->assertStatus(403);
    }

    public function test_admin_can_approve_and_refund_restoring_stock(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'delivered',
            'payment_method' => 'cod',
            'total_amount' => 100,
        ]);
        Cart::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50,
            'amount' => 100,
            'order_id' => $order->id,
        ]);

        $return = OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => 'Damaged on arrival, requesting my money back.',
        ]);

        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('order-returns.approve', $return))
            ->assertRedirect();
        $this->assertEquals(OrderReturn::STATUS_APPROVED, $return->fresh()->status);

        $this->actingAs($admin)
            ->post(route('order-returns.refund', $return))
            ->assertRedirect();

        $return->refresh();
        $order->refresh();

        $this->assertEquals(OrderReturn::STATUS_REFUNDED, $return->status);
        $this->assertEquals(100.0, $return->refund_amount);
        $this->assertEquals('refunded', $order->status);
        $this->assertEquals('refunded', $order->payment_status);
        // Stock came back: 3 + 2 returned units.
        $this->assertEquals(5, $product->fresh()->stock);

        Notification::assertSentTo($user, OrderReturnStatusNotification::class);
    }

    public function test_non_admin_cannot_manage_returns(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id, 'status' => 'delivered']);
        $return = OrderReturn::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'reason' => 'Trying to approve my own return request.',
        ]);

        $this->actingAs($user)->post(route('order-returns.approve', $return))->assertStatus(403);
        $this->actingAs($user)->get(route('order-returns.index'))->assertStatus(403);
    }
}
