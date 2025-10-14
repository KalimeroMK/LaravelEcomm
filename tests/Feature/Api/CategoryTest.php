<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\Category\Models\Category;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public string $url = '/api/v1/categories';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);

        // Create and assign category permissions
        $permissions = [
            'category-list',
            'category-create',
            'category-update',
            'category-delete',
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
    public function test_create_category(): TestResponse
    {
        $category = Category::factory()->create();
        $data = [
            'title' => $this->faker->word,
            'status' => 'active',
            'parent_id' => null,
            '_lft' => $this->faker->randomNumber(),
            '_rgt' => $this->faker->randomNumber(),
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update product.
     */
    // #[Test]
    public function test_update_category(): TestResponse
    {
        $category = Category::factory()->create();
        $parentCategory = Category::factory()->create();

        $data = [
            'parent_id' => $parentCategory->id,
        ];

        return $this->updatePUT($this->url, $data, $category->id);
    }

    /**
     * test find product.
     */
    #[Test]
    public function test_find_category(): TestResponse
    {
        $category = Category::factory()->create();
        $id = $category->id;

        return $this->show($this->url, $id);
    }

    /**
     * test get all products.
     */
    #[Test]
    public function test_get_all_category(): TestResponse
    {
        Category::factory()->count(3)->create();

        return $this->list($this->url);
    }

    /**
     * test delete products.
     */
    #[Test]
    public function test_delete_category(): TestResponse
    {
        $category = Category::factory()->create();
        $id = $category->id;

        return $this->destroy($this->url, $id);
    }

    #[Test]
    public function test_structure()
    {
        Category::factory()->count(2)->create();
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/categories');
        $response->assertStatus(200);

        $response->assertJsonStructure(
            [
                'data' => [
                    0 => [
                        'id',
                        'title',
                        'slug',
                        'status',
                        '_lft',
                        '_rgt',
                        'created_at',
                        'updated_at',
                        'parent_id',
                    ],
                ],

            ]
        );
    }
}
