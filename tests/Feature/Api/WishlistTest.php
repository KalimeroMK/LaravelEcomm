<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create user for authenticated tests
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test wishlist index without authentication.
     */
    #[Test]
    public function test_wishlist_index_without_auth(): void
    {
        $response = $this->json('GET', '/api/v1/wishlist');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    /**
     * test wishlist index with authentication.
     */
    #[Test]
    public function test_wishlist_index_with_auth(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/wishlist');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'wishlist',
                'statistics',
                'count',
            ],
        ]);
    }

    /**
     * test wishlist store without authentication.
     */
    #[Test]
    public function test_wishlist_store_without_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('POST', '/api/v1/wishlist', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    /**
     * test wishlist store with authentication.
     */
    #[Test]
    public function test_wishlist_store_with_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/v1/wishlist', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Product added to wishlist successfully',
        ]);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'product_id',
                'user_id',
                'quantity',
            ],
        ]);
    }

    /**
     * test wishlist count without authentication.
     */
    #[Test]
    public function test_wishlist_count_without_auth(): void
    {
        $response = $this->json('GET', '/api/v1/wishlist/count');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'count' => 0,
            ],
        ]);
    }

    /**
     * test wishlist count with authentication.
     */
    #[Test]
    public function test_wishlist_count_with_auth(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/wishlist/count');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'count',
            ],
        ]);
    }

    /**
     * test wishlist check without authentication.
     */
    #[Test]
    public function test_wishlist_check_without_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('GET', "/api/v1/wishlist/check/{$product->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'in_wishlist' => false,
                'product_id' => $product->id,
            ],
        ]);
    }

    /**
     * test wishlist check with authentication.
     */
    #[Test]
    public function test_wishlist_check_with_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', "/api/v1/wishlist/check/{$product->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'in_wishlist',
                'product_id',
            ],
        ]);
    }

    /**
     * test duplicate product in wishlist.
     */
    #[Test]
    public function test_duplicate_product_in_wishlist(): void
    {
        $product = Product::factory()->create();

        // Add product to wishlist first time
        $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/v1/wishlist', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Try to add same product again
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('POST', '/api/v1/wishlist', [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Product is already in your wishlist',
        ]);
    }
}
