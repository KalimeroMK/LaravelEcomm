<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Modules\Cart\Models\Cart;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class CartTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;

    public string $url = '/api/v1/carts';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign cart permissions
        $permissions = [
            'cart-list',
            'cart-create',
            'cart-update',
            'cart-delete',
        ];

        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($adminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
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
        $id = Cart::factory()->create([
            'product_id' => $product->id,
            'user_id' => $this->user->id, // Use the authenticated user
        ])->id;

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
        $cart = Cart::factory()->create([
            'user_id' => $this->user->id, // Use the authenticated user
        ]);
        $id = $cart->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        $user = User::factory()->create();
        Cart::factory()->count(2)->create(['user_id' => $user->id]);
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/carts');
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
