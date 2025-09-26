<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Spatie\Permission\Models\Role;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use WithFaker;
    use RefreshDatabase;

    public string $url = '/api/v1/roles';
    
    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create super-admin user with permissions
        $this->user = User::factory()->create();
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
        
        // Create and assign role permissions
        $permissions = [
            'role-list',
            'role-create', 
            'role-update',
            'role-delete'
        ];
        
        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $superAdminRole->givePermissionTo($perm);
        }
        
        $this->user->assignRole($superAdminRole);
        
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test create role.
     */
    #[Test]
    public function test_create_role(): TestResponse
    {
        $data = [
            'name' => 'test-role-' . time(),
            'guard_name' => 'web',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update role.
     */
    #[Test]
    public function test_update_role(): TestResponse
    {
        $role = Role::create(['name' => 'test-role-' . time()]);
        $data = [
            'name' => 'updated-role-' . time(),
            'guard_name' => 'web',
        ];

        return $this->updatePUT($this->url, $data, $role->id);
    }

    /**
     * test find role.
     */
    #[Test]
    public function test_find_role(): TestResponse
    {
        $role = Role::create(['name' => 'test-role-' . time()]);

        return $this->show($this->url, $role->id);
    }

    /**
     * test get all roles.
     */
    #[Test]
    public function test_get_all_roles(): TestResponse
    {
        Role::create(['name' => 'test-role-1-' . time()]);
        Role::create(['name' => 'test-role-2-' . time()]);
        Role::create(['name' => 'test-role-3-' . time()]);

        return $this->list($this->url);
    }

    /**
     * test delete role.
     */
    #[Test]
    public function test_delete_role(): TestResponse
    {
        $role = Role::create(['name' => 'test-role-' . time()]);

        return $this->destroy($this->url, $role->id);
    }

    #[Test]
    public function test_structure(): void
    {
        Role::create(['name' => 'test-role-1-' . time()]);
        Role::create(['name' => 'test-role-2-' . time()]);
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/roles');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'guard_name',
                ],
            ],
        ]);
    }
}
