<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Shipping\Models\Shipping;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public string $url = '/api/v1/shipping';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign shipping permissions
        $permissions = [
            'shipping-list',
            'shipping-create',
            'shipping-update',
            'shipping-delete',
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
    public function test_create_shipping(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'type' => Carbon::now().$this->faker->unique()->word,
            'price' => $this->faker->numberBetween(1, 100),
            'status' => 'active',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    #[Test]
    public function test_update_shipping(): TestResponse
    {
        $shipping = Shipping::factory()->create();
        $data = [
            'type' => 'express',
            'price' => '3223',
            'status' => 'active',
        ];

        $id = $shipping->id;

        return $this->updatePUT($this->url, $data, $id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_shipping(): TestResponse
    {
        $id = Shipping::factory()->create()->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_shipping(): TestResponse
    {
        Shipping::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_shipping(): TestResponse
    {
        $id = Shipping::factory()->create()->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure(): void
    {
        Shipping::factory()->count(2)->create();
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/shipping');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'type',
                        'price',
                        'status',
                        'created_at',
                        'updated_at',
                    ],
                ],

            ]
        );
    }
}
