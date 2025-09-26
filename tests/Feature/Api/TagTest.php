<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class TagTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/tags/';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign tag permissions
        $permissions = [
            'tag-list',
            'tag-create', 
            'tag-update',
            'tag-delete'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($adminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function test_create_tag()
    {
        $data = [
            'title' => $this->faker->unique()->word(),
            'slug' => $this->faker->slug(),
            'status' => 'active',
        ];

        return $this->create($this->url, $data);
    }

    #[Test]
    public function test_update_tag()
    {
        $tag = Tag::factory()->create();
        $data = [
            'title' => $this->faker->unique()->word,
            'status' => 'inactive',
        ];
        $response = $this->updatePUT($this->url, $data, $tag->id);
        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $tag->id]]);
    }

    #[Test]
    public function test_find_tag()
    {
        $tag = Tag::factory()->create();
        $response = $this->show($this->url, $tag->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'slug',
                'status',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    #[Test]
    public function test_get_all_tags()
    {
        Tag::factory()->count(2)->create();
        $response = $this->list($this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    #[Test]
    public function test_delete_tag()
    {
        $tag = Tag::factory()->create();
        $response = $this->destroy($this->url, $tag->id);
        $response->assertStatus(200);
    }

    #[Test]
    public function test_structure()
    {
        Tag::factory()->count(2)->create();
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/tags');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'slug',
                    'status',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
