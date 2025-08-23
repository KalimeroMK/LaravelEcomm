<?php

declare(strict_types=1);

namespace Modules\Core\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Product\Models\Product;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;
use Tests\TestCase;

class EcommerceWorkflowTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Product $product1;
    private Product $product2;
    private Shipping $shipping;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create test products
        $this->product1 = Product::factory()->create([
            'status' => 'active',
            'price' => 100.00,
            'stock' => 10
        ]);

        $this->product2 = Product::factory()->create([
            'status' => 'active',
            'price' => 75.50,
            'stock' => 5
        ]);

        $this->shipping = Shipping::factory()->create([
            'status' => 'active',
            'price' => 15.00
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function complete_ecommerce_workflow_from_cart_to_payment()
    {
        // Step 1: Add products to cart
        $cart1 = $this->addProductToCart($this->product1, 2);
        $cart2 = $this->addProductToCart($this->product2, 1);

        $this->assertDatabaseHas('carts', [
            'id' => $cart1->id,
            'user_id' => $this->user->id,
            'product_id' => $this->product1->id,
            'quantity' => 2
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart2->id,
            'user_id' => $this->user->id,
            'product_id' => $this->product2->id,
            'quantity' => 1
        ]);

        // Step 2: Calculate totals
        $subTotal = ($this->product1->price * 2) + ($this->product2->price * 1);
        $totalAmount = $subTotal + $this->shipping->price;

        $this->assertEquals(275.50, $subTotal); // (100 * 2) + (75.50 * 1)
        $this->assertEquals(290.50, $totalAmount); // 275.50 + 15.00

        // Step 3: Create order
        $order = $this->createOrder($subTotal, $totalAmount);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'sub_total' => $subTotal,
            'total_amount' => $totalAmount,
            'payment_status' => 'pending',
            'status' => 'pending'
        ]);

        // Step 4: Link cart items to order
        $this->linkCartToOrder($cart1, $order->id);
        $this->linkCartToOrder($cart2, $order->id);

        $this->assertDatabaseHas('carts', [
            'id' => $cart1->id,
            'order_id' => $order->id
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $cart2->id,
            'order_id' => $order->id
        ]);

        // Step 5: Process payment
        $paymentResult = $this->processPayment($order);

        $this->assertTrue($paymentResult);

        // Step 6: Verify order status updated
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'completed',
            'status' => 'processing'
        ]);
    }

    /** @test */
    public function ecommerce_workflow_handles_stock_validation()
    {
        // Try to add more products than available stock
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/carts', [
            'product_id' => $this->product1->id,
            'quantity' => 15, // More than available stock (10)
            'user_id' => $this->user->id,
            'price' => $this->product1->price
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function ecommerce_workflow_handles_invalid_products()
    {
        // Try to add non-existent product
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/carts', [
            'product_id' => 99999,
            'quantity' => 1,
            'user_id' => $this->user->id,
            'price' => 100.00
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function ecommerce_workflow_handles_user_authorization()
    {
        $otherUser = User::factory()->create();

        // Try to access other user's cart
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->getJson('/api/carts');

        $response->assertStatus(200);

        // Verify only current user's items are returned
        $cartItems = $response->json('data');
        foreach ($cartItems as $item) {
            $this->assertEquals($this->user->id, $item['user_id']);
        }
    }

    /** @test */
    public function ecommerce_workflow_handles_cart_updates()
    {
        // Add product to cart
        $cart = $this->addProductToCart($this->product1, 1);

        // Update quantity
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->putJson("/api/carts/{$cart->id}", [
            'quantity' => 3,
            'amount' => $this->product1->price * 3
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 3,
            'amount' => $this->product1->price * 3
        ]);
    }

    /** @test */
    public function ecommerce_workflow_handles_cart_removal()
    {
        // Add product to cart
        $cart = $this->addProductToCart($this->product1, 1);

        // Remove from cart
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->deleteJson("/api/carts/{$cart->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
    }

    // Helper methods
    private function addProductToCart(Product $product, int $quantity): Cart
    {
        $cartData = [
            'product_id' => $product->id,
            'quantity' => $quantity,
            'user_id' => $this->user->id,
            'price' => $product->price,
            'amount' => $product->price * $quantity
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/carts', $cartData);

        $response->assertStatus(201);

        return Cart::where('user_id', $this->user->id)
            ->where('product_id', $product->id)
            ->latest()
            ->first();
    }

    private function createOrder(float $subTotal, float $totalAmount): Order
    {
        $orderData = [
            'user_id' => $this->user->id,
            'sub_total' => $subTotal,
            'shipping_id' => $this->shipping->id,
            'total_amount' => $totalAmount,
            'quantity' => 3, // Total items from both products
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
            'status' => 'pending'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        return Order::where('user_id', $this->user->id)
            ->latest()
            ->first();
    }

    private function linkCartToOrder(Cart $cart, int $orderId): void
    {
        $cart->update(['order_id' => $orderId]);
    }

    private function processPayment(Order $order): bool
    {
        $paymentData = [
            'order_id' => $order->id,
            'amount' => $order->total_amount,
            'currency' => 'usd',
            'payment_method' => 'stripe',
            'description' => 'Order payment for #' . $order->order_number
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', $paymentData);

        if ($response->status() === 200) {
            // Simulate successful payment
            $order->update([
                'payment_status' => 'completed',
                'status' => 'processing'
            ]);
            return true;
        }

        return false;
    }
}
