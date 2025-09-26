<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Modules\Attribute\Models\AttributeGroup;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class AttributeGroupTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/attribute-groups/';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign attribute group permissions
        $permissions = [
            'attribute-group-list',
            'attribute-group-create', 
            'attribute-group-update',
            'attribute-group-delete'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($adminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function create_attribute_group(): TestResponse
    {
        $data = [
            'name' => 'mame-'.mb_strtoupper(Str::random(10)),
        ];

        return $this->create($this->url, $data);
    }

    #[Test]
    public function update_attribute_group(): void
    {
        $data = [
            'name' => 'mame-'.mb_strtoupper(Str::random(10)),
        ];
        $id = AttributeGroup::factory()->create()->id;

        $response = $this->updatePUT($this->url, $data, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function find_attribute_group(): void
    {
        $id = AttributeGroup::factory()->create()->id;

        $response = $this->show($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function get_all_attribute_group(): void
    {
        $response = $this->list($this->url);
        $response->assertStatus(200);
    }

    #[Test]
    public function delete_attribute_group(): void
    {
        $id = AttributeGroup::factory()->create()->id;

        $response = $this->destroy($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function structure_group(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', $this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
}
