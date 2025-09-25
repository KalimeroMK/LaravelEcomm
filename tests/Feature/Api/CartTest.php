<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\TestResponse;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\BaseTestTrait;
use Tests\TestCase;

class CartTest extends TestCase
{
    use BaseTestTrait;
    use RefreshDatabase;
    use WithoutMiddleware;

    public string $url = '/api/v1/carts';

    /** @var User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a super-admin user and authenticate
        $this->user = User::factory()->create();
        $this->user->assignRole('super-admin');
        $this->actingAs($this->user);
    }

    /**
     * test create product.
     */
    #[Test]
    public function test_create_cart(): TestResponse
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();

        $data = [
            'slug' => $product->slug,
            'quantity' => 5,
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_cart(): TestResponse
    {
        $product = Product::factory()->create();
        $user = User::factory()->create();
        $id = Cart::factory()->create(['product_id' => $product->id, 'user_id' => $user->id])->id;

        $data = [
            'slug' => $product->slug,
            'quantity' => 5,
        ];

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_cart(): TestResponse
    {
        $cart = Cart::factory()->create();
        $id = $cart->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_cart(): TestResponse
    {
        $user = User::factory()->create();
        Cart::factory()->count(3)->create(['user_id' => $user->id]);

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_cart(): TestResponse
    {
        $cart = Cart::factory()->create();
        $id = $cart->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        $user = User::factory()->create();
        Cart::factory()->count(2)->create(['user_id' => $user->id]);
        $response = $this->json('GET', '/api/v1/carts');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'price',
                        'status',
                        'quantity',
                        'amount',
                        'created_at',
                        'updated_at',
                        'wishlists_count',
                        'product_id',
                        'order_id',
                        'user_id',
                        'session_id',
                    ],
                ],

            ]
        );
    }
}
