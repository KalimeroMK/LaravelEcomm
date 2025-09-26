<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/orders';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign order permissions
        $permissions = [
            'order-list',
            'order-create', 
            'order-update',
            'order-delete'
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
    public function test_create_order(): TestResponse
    {
        $data = Order::factory()->make([
            'user_id' => User::factory()->create()->id,
            'shipping_id' => Shipping::factory()->create()->id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ])->toArray();

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_order(): TestResponse
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id, // Use the authenticated user
        ]);
        $data = Order::factory()->make([
            'user_id' => $this->user->id,
            'shipping_id' => $order->shipping_id,
            'status' => 'pending',
            'payment_status' => 'pending',
        ])->toArray();
        $id = $order->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_order(): TestResponse
    {
        $order = Order::factory()->create();
        $id = $order->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_orders(): TestResponse
    {
        Order::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_order(): TestResponse
    {
        $order = Order::factory()->create([
            'user_id' => $this->user->id, // Use the authenticated user
        ]);
        $id = $order->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure(): void
    {
        // Create orders
        $orders = Order::factory()->count(2)->create();

        // Create carts for each order
        foreach ($orders as $order) {
            Cart::factory()->create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);
        }

        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/orders');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'order_number',
                    'sub_total',
                    'total_amount',
                    'quantity',
                    'payment_method',
                    'payment_status',
                    'status',
                    'created_at',
                    'updated_at',
                    'cart_info_count',
                    'carts_count',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'status',
                    ],
                    'shipping' => [
                        'id',
                        'type',
                        'price',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                    'carts' => [
                        '*' => [
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
                        ],
                    ],
                ],
            ],
            'meta',
            'links',
        ]);
    }
}
