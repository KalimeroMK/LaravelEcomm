<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Product\Models\Product;
use Modules\Product\Services\ElasticsearchService;
use Modules\Product\Services\RecommendationService;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class AdvancedFeaturesTest extends TestCase
{
    use BaseTestTrait;
    use RefreshDatabase;
    use WithoutMiddleware;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock ElasticsearchService to avoid Elasticsearch dependency
        $this->mock(ElasticsearchService::class, function ($mock) {
            $mock->shouldReceive('search')->andReturn(collect([]));
            $mock->shouldReceive('index')->andReturn(true);
            $mock->shouldReceive('delete')->andReturn(true);
            $mock->shouldReceive('createIndex')->andReturn(true);
        });

        // Mock RecommendationService
        $this->mock(RecommendationService::class, function ($mock) {
            $mock->shouldReceive('getRecommendations')->andReturn([]);
            $mock->shouldReceive('getRelatedProducts')->andReturn([]);
            $mock->shouldReceive('getAIRecommendations')->andReturn(collect([]));
            $mock->shouldReceive('getCollaborativeRecommendations')->andReturn(collect([]));
            $mock->shouldReceive('getContentBasedRecommendations')->andReturn(collect([]));
        });

        // Create a super-admin user and authenticate
        $this->user = User::factory()->create();
        $this->user->assignRole('super-admin');
        $this->actingAs($this->user);
    }

    /** @var User */
    private $user;

    /**
     * Test advanced search functionality
     */
    #[Test]
    public function test_advanced_search(): TestResponse
    {
        // Create test products
        $product1 = Product::factory()->create([
            'title' => 'Gaming Laptop',
            'price' => 1200.00,
            'status' => 'active'
        ]);

        $product2 = Product::factory()->create([
            'title' => 'Office Laptop',
            'price' => 800.00,
            'status' => 'active'
        ]);

        $data = [
            'query' => 'laptop',
            'price_min' => 500,
            'price_max' => 1000,
            'status' => 'active'
        ];

        return $this->post('/api/v1/search', $data);
    }

    /**
     * Test search suggestions
     */
    #[Test]
    public function test_search_suggestions(): TestResponse
    {
        // Create test products for suggestions
        Product::factory()->create(['title' => 'Gaming Laptop']);
        Product::factory()->create(['title' => 'Office Laptop']);

        return $this->get('/api/v1/search/suggestions?query=laptop');
    }

    /**
     * Test search filters
     */
    #[Test]
    public function test_search_filters(): TestResponse
    {
        return $this->get('/api/v1/search/filters?query=laptop');
    }

    /**
     * Test product recommendations
     */
    #[Test]
    public function test_product_recommendations(): TestResponse
    {
        // Create test products
        Product::factory()->count(5)->create();

        return $this->get('/api/v1/recommendations?type=ai&limit=5');
    }

    /**
     * Test related products
     */
    #[Test]
    public function test_related_products(): TestResponse
    {
        $product = Product::factory()->create();

        return $this->get("/api/v1/recommendations/related/{$product->id}?limit=5");
    }

    /**
     * Test enhanced wishlist functionality
     */
    #[Test]
    public function test_enhanced_wishlist(): TestResponse
    {
        $product = Product::factory()->create();

        $data = [
            'product_id' => $product->id,
            'quantity' => 2
        ];

        return $this->post('/api/v1/wishlist', $data);
    }

    /**
     * Test wishlist count
     */
    #[Test]
    public function test_wishlist_count(): TestResponse
    {
        return $this->get('/api/v1/wishlist/count');
    }

    /**
     * Test wishlist check
     */
    #[Test]
    public function test_wishlist_check(): TestResponse
    {
        $product = Product::factory()->create();

        return $this->get("/api/v1/wishlist/check/{$product->id}");
    }

    /**
     * Test wishlist recommendations
     */
    #[Test]
    public function test_wishlist_recommendations(): TestResponse
    {
        return $this->get('/api/v1/wishlist/recommendations?limit=5');
    }

    /**
     * Test wishlist price alerts
     */
    #[Test]
    public function test_wishlist_price_alerts(): TestResponse
    {
        return $this->get('/api/v1/wishlist/price-alerts');
    }

    /**
     * Test bulk wishlist operations
     */
    #[Test]
    public function test_bulk_wishlist_operations(): TestResponse
    {
        $products = Product::factory()->count(3)->create();
        $productIds = $products->pluck('id')->toArray();

        $data = [
            'action' => 'remove',
            'product_ids' => $productIds
        ];

        return $this->post('/api/v1/wishlist/bulk-operations', $data);
    }

    /**
     * Test wishlist sharing
     */
    #[Test]
    public function test_wishlist_sharing(): TestResponse
    {
        $recipient = User::factory()->create();

        $data = [
            'recipient_email' => $recipient->email
        ];

        return $this->post('/api/v1/wishlist/share', $data);
    }

    /**
     * Test public wishlist access
     */
    #[Test]
    public function test_public_wishlist(): TestResponse
    {
        $user = User::factory()->create();

        return $this->get("/api/v1/wishlist/public/{$user->id}");
    }

    /**
     * Test search structure validation
     */
    #[Test]
    public function test_search_structure(): TestResponse
    {
        $response = $this->post('/api/v1/search', [
            'query' => 'test',
            'per_page' => 10
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'products',
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page'
                ],
                'filters_applied',
                'search_query'
            ]
        ]);

        return $response;
    }

    /**
     * Test recommendations structure validation
     */
    #[Test]
    public function test_recommendations_structure(): TestResponse
    {
        $response = $this->get('/api/v1/recommendations?type=ai&limit=5');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'recommendations',
                'type',
                'count'
            ]
        ]);

        return $response;
    }

    /**
     * Test wishlist structure validation
     */
    #[Test]
    public function test_wishlist_structure(): TestResponse
    {
        $response = $this->get('/api/v1/wishlist');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'wishlist',
                'statistics' => [
                    'total_items',
                    'total_value',
                    'categories',
                    'brands',
                    'price_range' => [
                        'min',
                        'max',
                        'avg'
                    ]
                ],
                'count'
            ]
        ]);

        return $response;
    }
}
