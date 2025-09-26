<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Attribute\Models\Attribute;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class AttributeTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/attributes/';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user with permissions
        $this->user = User::factory()->create();
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        
        // Create and assign attribute permissions
        $permissions = [
            'attribute-list',
            'attribute-create', 
            'attribute-update',
            'attribute-delete'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $adminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($adminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function create_attribute(): void
    {
        $response = $this->create($this->url, Attribute::factory()->make()->toArray());
        $response->assertStatus(200);
    }

    #[Test]
    public function update_attribute(): void
    {
        $data = Attribute::factory()->make()->toArray();
        $id = Attribute::factory()->create()->id;

        $response = $this->updatePUT($this->url, $data, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function find_attribute(): void
    {
        $id = Attribute::factory()->create()->id;

        $response = $this->show($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function get_all_attribute(): void
    {
        $response = $this->list($this->url);
        $response->assertStatus(200);
    }

    #[Test]
    public function delete_attribute(): void
    {
        $id = Attribute::factory()->create()->id;

        $response = $this->destroy($this->url, $id);
        $response->assertStatus(200);
    }

    #[Test]
    public function structure(): void
    {
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/attributes/');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'code',
                    'type',
                    'display',
                    'filterable',
                    'configurable',
                ],
            ],
        ]);
    }

    #[Test]
    public function flexible_attribute_values_are_returned(): void
    {
        $attribute = Attribute::factory()->create([
            'type' => 'text',
        ]);

        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', "/api/v1/attributes/{$attribute->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $attribute->id,
            'type' => 'text',
        ]);
    }
}
