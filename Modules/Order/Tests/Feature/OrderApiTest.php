<?php

declare(strict_types=1);

namespace Modules\Order\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Product $product;
    private Shipping $shipping;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->product = Product::factory()->create([
            'status' => 'active',
            'price' => 100.00,
            'stock' => 10
        ]);

        $this->shipping = Shipping::factory()->create([
            'status' => 'active',
            'price' => 15.00
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function user_can_create_order_from_cart()
    {
        // Create cart items
        $cart1 = Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => $this->product->price,
            'amount' => $this->product->price * 2
        ]);

        $orderData = [
            'user_id' => $this->user->id,
            'sub_total' => $cart1->amount,
            'shipping_id' => $this->shipping->id,
            'total_amount' => $cart1->amount + $this->shipping->price,
            'quantity' => $cart1->quantity,
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
            'status' => 'pending'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'sub_total' => $cart1->amount,
            'total_amount' => $cart1->amount + $this->shipping->price
        ]);
    }

    /** @test */
    public function user_can_view_their_orders()
    {
        Order::factory()->create([
            'user_id' => $this->user->id,
            'sub_total' => 200.00,
            'total_amount' => 215.00,
            'status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/orders');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_view_specific_order()
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'sub_total' => 200.00,
            'total_amount' => 215.00,
            'status' => 'pending'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_view_other_users_order()
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
            'sub_total' => 200.00,
            'total_amount' => 215.00
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function order_validates_required_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/orders', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function order_calculates_totals_correctly()
    {
        $cart1 = Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price' => $this->product->price,
            'amount' => $this->product->price * 3
        ]);

        $orderData = [
            'user_id' => $this->user->id,
            'sub_total' => $cart1->amount,
            'shipping_id' => $this->shipping->id,
            'total_amount' => $cart1->amount + $this->shipping->price,
            'quantity' => $cart1->quantity,
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
            'status' => 'pending'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        $expectedSubTotal = $this->product->price * 3;
        $expectedTotal = $expectedSubTotal + $this->shipping->price;

        $this->assertDatabaseHas('orders', [
            'sub_total' => $expectedSubTotal,
            'total_amount' => $expectedTotal
        ]);
    }
}
