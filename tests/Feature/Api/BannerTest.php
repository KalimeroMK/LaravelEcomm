<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Modules\Banner\Models\Banner;
use Modules\User\Models\User;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class BannerTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public string $url = '/api/v1/banners/';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign banner permissions (BannerPolicy uses brand permissions)
        $permissions = [
            'brand-list',
            'brand-create',
            'brand-update',
            'brand-delete',
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
    public function test_create_banner(): TestResponse
    {
        Storage::fake('uploads');

        $data = [
            'title' => $this->faker->unique()->word,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->text,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'images' => [UploadedFile::fake()->image('updated_file.png', 600, 600)],
            'status' => 'inactive',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    public function test_update_banner(): TestResponse
    {
        $banner = Banner::factory()->create();

        $data = [
            'title' => time().'Test title',
            'description' => time().'test-description',
            'status' => 'inactive',
            'images' => [UploadedFile::fake()->image('updated_file.png', 600, 600)],
        ];

        return $this->updatePUT($this->url, $data, $banner->id);
    }

    /**
     * test find product.
     */
    public function test_find_banner(): TestResponse
    {
        $banner = Banner::factory()->create();

        return $this->show($this->url, $banner->id);
    }

    /**
     * test get all products.
     */
    public function test_get_all_banners(): TestResponse
    {
        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    public function test_delete_banner(): TestResponse
    {
        $banner = Banner::factory()->create();

        return $this->destroy($this->url, $banner->id);
    }

    public function test_structure()
    {
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/banners/');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'images',
                    'description',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
