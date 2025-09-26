<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Coupon\Models\Coupon;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/coupons';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign coupon permissions
        $permissions = [
            'coupon-list',
            'coupon-create', 
            'coupon-update',
            'coupon-delete',
            'coupon-show'
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
    public function test_create_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $data = [
            'code' => $this->faker->word,
            'value' => $this->faker->randomFloat(),
            'type' => 'fixed',
            'status' => 'active',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $data = [
            'code' => $this->faker->word,
            'value' => $this->faker->randomFloat(),
            'type' => 'percent',
            'status' => 'active',
            'value' => $this->faker->randomFloat(),

        ];

        $id = $coupon->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $id = $coupon->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_coupon(): TestResponse
    {
        Coupon::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_coupon(): TestResponse
    {
        $coupon = Coupon::factory()->create();
        $id = $coupon->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Coupon::factory()->count(2)->create();
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/coupons');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'code',
                        'type',
                        'value',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
