<?php

declare(strict_types=1);

namespace Modules\Cart\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class CartApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private Product $product;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->product = Product::factory()->create([
            'status' => 'active',
            'price' => 100.00,
            'stock' => 10,
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function user_can_add_product_to_cart(): void
    {
        $cartData = [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'user_id' => $this->user->id,
            'price' => $this->product->price,
            'amount' => $this->product->price * 2,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/carts', $cartData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('carts', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function user_can_view_their_cart(): void
    {
        Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/carts');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_update_cart_item(): void
    {
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $updateData = [
            'quantity' => 3,
            'amount' => $this->product->price * 3,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->putJson("/api/carts/{$cart->id}", $updateData);

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_remove_item_from_cart(): void
    {
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/carts/{$cart->id}");

        $response->assertStatus(200);
    }
}
