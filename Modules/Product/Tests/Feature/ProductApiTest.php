<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /** @test */
    public function user_can_view_all_products(): void
    {
        Product::factory()->count(5)->create([
            'status' => 'active',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(5, $products);
    }

    /** @test */
    public function user_can_view_specific_product(): void
    {
        $product = Product::factory()->create([
            'status' => 'active',
            'title' => 'Test Product',
            'price' => 99.99,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson("/api/products/{$product->id}");

        $response->assertStatus(200);

        $productData = $response->json('data');
        $this->assertEquals('Test Product', $productData['title']);
        $this->assertEquals(99.99, $productData['price']);
    }

    /** @test */
    public function user_can_search_products(): void
    {
        Product::factory()->create([
            'title' => 'iPhone 15 Pro',
            'status' => 'active',
        ]);

        Product::factory()->create([
            'title' => 'Samsung Galaxy S24',
            'status' => 'active',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products?search=iPhone');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(1, $products);
        $this->assertEquals('iPhone 15 Pro', $products[0]['title']);
    }

    /** @test */
    public function user_can_filter_products_by_status(): void
    {
        Product::factory()->create(['status' => 'active']);
        Product::factory()->create(['status' => 'inactive']);
        Product::factory()->create(['status' => 'active']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products?status=active');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(2, $products);

        foreach ($products as $product) {
            $this->assertEquals('active', $product['status']);
        }
    }

    /** @test */
    public function user_can_filter_products_by_price_range(): void
    {
        Product::factory()->create(['price' => 50.00, 'status' => 'active']);
        Product::factory()->create(['price' => 150.00, 'status' => 'active']);
        Product::factory()->create(['price' => 300.00, 'status' => 'active']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products?min_price=100&max_price=200');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(1, $products);
        $this->assertEquals(150.00, $products[0]['price']);
    }

    /** @test */
    public function user_can_sort_products_by_price(): void
    {
        Product::factory()->create(['price' => 300.00, 'status' => 'active']);
        Product::factory()->create(['price' => 100.00, 'status' => 'active']);
        Product::factory()->create(['price' => 200.00, 'status' => 'active']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products?sort_by=price&sort_order=asc');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertEquals(100.00, $products[0]['price']);
        $this->assertEquals(200.00, $products[1]['price']);
        $this->assertEquals(300.00, $products[2]['price']);
    }

    /** @test */
    public function user_can_view_featured_products(): void
    {
        Product::factory()->create([
            'is_featured' => true,
            'status' => 'active',
        ]);

        Product::factory()->create([
            'is_featured' => false,
            'status' => 'active',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products?featured=true');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(1, $products);
        $this->assertTrue($products[0]['is_featured']);
    }

    /** @test */
    public function user_can_view_products_with_categories(): void
    {
        $product = Product::factory()->create([
            'status' => 'active',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson("/api/products/{$product->id}?include=categories");

        $response->assertStatus(200);

        $productData = $response->json('data');
        $this->assertArrayHasKey('categories', $productData);
    }

    /** @test */
    public function user_cannot_view_inactive_products_by_default(): void
    {
        Product::factory()->create(['status' => 'active']);
        Product::factory()->create(['status' => 'inactive']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson('/api/products');

        $response->assertStatus(200);

        $products = $response->json('data');
        $this->assertCount(1, $products);
        $this->assertEquals('active', $products[0]['status']);
    }

    /** @test */
    public function product_returns_correct_structure(): void
    {
        $product = Product::factory()->create([
            'status' => 'active',
            'title' => 'Test Product',
            'price' => 99.99,
            'stock' => 10,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'price',
                    'stock',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }
}
