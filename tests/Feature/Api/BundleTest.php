<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Bundle\Models\Bundle;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class BundleTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public string $url = '/api/v1/bundles';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign bundle permissions
        $permissions = [
            'bundle-list',
            'bundle-create',
            'bundle-update',
            'bundle-delete',
            'bundle-show',
        ];

        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($adminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function test_create_bundle(): TestResponse
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $data = [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'products' => [$product->id],
            'images' => [UploadedFile::fake()->image('bundle.jpg')],
        ];

        return $this->create($this->url, $data);
    }

    #[Test]
    public function test_update_bundle(): void
    {
        Storage::fake('public');
        $bundle = Bundle::factory()->create();
        $product = Product::factory()->create();
        $data = [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'products' => [$product->id],
            'images' => [UploadedFile::fake()->image('bundle_update.jpg')],
        ];
        $response = $this->updatePUT($this->url, $data, $bundle->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'products',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_show_bundle(): void
    {
        $bundle = Bundle::factory()->create();
        $response = $this->show($this->url, $bundle->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
                'price',
                'products',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_delete_bundle(): void
    {
        $bundle = Bundle::factory()->create();
        $response = $this->destroy($this->url, $bundle->id);
        $response->assertStatus(200);
    }

    #[Test]
    public function test_index_structure(): void
    {
        Bundle::factory()->count(2)->create();
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', $this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'products',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
