<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductStatsTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign product stats permissions (using product permissions)
        $permissions = [
            'product-list',
        ];

        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($adminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function test_get_all_product_stats_without_auth(): void
    {
        $response = $this->json('GET', '/api/v1/product-stats');

        $response->assertStatus(401);
    }

    #[Test]
    public function test_get_all_product_stats_with_auth(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/product-stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
        ]);
    }

    #[Test]
    public function test_get_all_product_stats_with_filters(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', '/api/v1/product-stats', [
            'from' => '2024-01-01',
            'to' => '2024-12-31',
            'order_by' => 'id',
            'sort' => 'desc',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
        ]);
    }

    #[Test]
    public function test_get_product_detail_stats_without_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('GET', "/api/v1/product-stats/{$product->id}/detail");

        $response->assertStatus(401);
    }

    #[Test]
    public function test_get_product_detail_stats_with_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', "/api/v1/product-stats/{$product->id}/detail");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'product',
                'impressions',
                'clicks',
                'stats',
            ],
        ]);
    }

    #[Test]
    public function test_get_product_detail_stats_with_date_range(): void
    {
        $product = Product::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
            'Accept' => 'application/json',
        ])->json('GET', "/api/v1/product-stats/{$product->id}/detail", [
            'from' => '2024-01-01',
            'to' => '2024-12-31',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'product',
                'impressions',
                'clicks',
                'stats',
            ],
        ]);
    }

    #[Test]
    public function test_store_product_impression_without_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('POST', '/api/v1/product-tracking/impressions', [
            'product_ids' => [$product->id],
        ]);

        // Product tracking can work without auth
        $response->assertStatus(200);
    }

    #[Test]
    public function test_store_product_click_without_auth(): void
    {
        $product = Product::factory()->create();

        $response = $this->json('POST', '/api/v1/product-tracking/click', [
            'product_id' => $product->id,
        ]);

        // Product tracking can work without auth
        $response->assertStatus(200);
    }
}
