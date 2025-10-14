<?php

declare(strict_types=1);

namespace Tests\Feature\Ecommerce;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
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

        $this->product = Product::factory()->create([
            'status' => 'active',
            'price' => 100.00,
            'stock' => 10,
        ]);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function user_can_add_product_to_cart()
    {
        $cartData = [
            'slug' => $this->product->slug,
            'quantity' => 2,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->postJson('/api/v1/carts', $cartData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('carts', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'quantity' => 2,
        ]);
    }

    #[Test]
    public function user_can_view_their_cart()
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
        ])->getJson('/api/v1/carts');

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_update_cart_item()
    {
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
            'amount' => $this->product->price,
        ]);

        $updateData = [
            'slug' => $this->product->slug,
            'quantity' => 3,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->putJson("/api/v1/carts/{$cart->id}", $updateData);

        $response->assertStatus(200);
    }

    #[Test]
    public function user_can_remove_item_from_cart()
    {
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => $this->product->price,
            'amount' => $this->product->price,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->deleteJson("/api/v1/carts/{$cart->id}");

        $response->assertStatus(200);
    }
}
