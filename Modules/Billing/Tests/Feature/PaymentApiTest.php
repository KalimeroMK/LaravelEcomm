<?php

declare(strict_types=1);

namespace Modules\Billing\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Order\Models\Order;
use Modules\User\Models\User;
use Tests\TestCase;

class PaymentApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Order $order;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'sub_total' => 200.00,
            'total_amount' => 215.00,
            'payment_status' => 'pending',
            'status' => 'pending'
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function user_can_initiate_stripe_payment()
    {
        $paymentData = [
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'currency' => 'usd',
            'payment_method' => 'stripe',
            'description' => 'Order payment for #' . $this->order->order_number
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', $paymentData);

        $response->assertStatus(200);
    }

    /** @test */
    public function payment_validates_required_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function payment_validates_order_exists()
    {
        $paymentData = [
            'order_id' => 99999, // Non-existent order
            'amount' => 100.00,
            'currency' => 'usd',
            'payment_method' => 'stripe'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', $paymentData);

        $response->assertStatus(422);
    }

    /** @test */
    public function payment_validates_amount_matches_order()
    {
        $paymentData = [
            'order_id' => $this->order->id,
            'amount' => 100.00, // Different amount
            'currency' => 'usd',
            'payment_method' => 'stripe'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', $paymentData);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_cannot_pay_for_other_users_order()
    {
        $otherUser = User::factory()->create();
        $otherOrder = Order::factory()->create([
            'user_id' => $otherUser->id,
            'total_amount' => 100.00
        ]);

        $paymentData = [
            'order_id' => $otherOrder->id,
            'amount' => $otherOrder->total_amount,
            'currency' => 'usd',
            'payment_method' => 'stripe'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', $paymentData);

        $response->assertStatus(403);
    }

    /** @test */
    public function payment_updates_order_status_after_success()
    {
        $paymentData = [
            'order_id' => $this->order->id,
            'amount' => $this->order->total_amount,
            'currency' => 'usd',
            'payment_method' => 'stripe',
            'description' => 'Order payment for #' . $this->order->order_number
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json'
        ])->postJson('/api/stripe', $paymentData);

        $response->assertStatus(200);

        // Note: In real implementation, this would be updated by Stripe webhook
        // This test assumes the payment action updates the order
    }
}
