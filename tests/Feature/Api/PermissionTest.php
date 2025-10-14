<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\TestResponse;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Permission;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public string $url = '/api/v1/permissions';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create super-admin user with permissions
        $this->user = User::factory()->create();
        $superAdminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);

        // Create and assign permission permissions
        $permissions = [
            'permission-list',
            'permission-create',
            'permission-update',
            'permission-delete',
        ];

        foreach ($permissions as $permission) {
            $perm = Permission::firstOrCreate(['name' => $permission]);
            $superAdminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($superAdminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    /**
     * test create permission.
     */
    #[Test]
    public function test_create_permission(): TestResponse
    {
        $data = [
            'name' => 'test-permission-'.time(),
            'guard_name' => 'web',
        ];

        return $this->create($this->url, $data);
    }

    /**
     * test update permission.
     */
    #[Test]
    public function test_update_permission(): TestResponse
    {
        $permission = Permission::create(['name' => 'test-permission-'.time()]);
        $data = [
            'name' => 'updated-permission-'.time(),
        ];

        return $this->updatePUT($this->url, $data, $permission->id);
    }

    /**
     * test find permission.
     */
    #[Test]
    public function test_find_permission(): TestResponse
    {
        $permission = Permission::create(['name' => 'test-permission-'.time()]);

        return $this->show($this->url, $permission->id);
    }

    /**
     * test get all permissions.
     */
    #[Test]
    public function test_get_all_permissions(): TestResponse
    {
        Permission::create(['name' => 'test-permission-1-'.time()]);
        Permission::create(['name' => 'test-permission-2-'.time()]);
        Permission::create(['name' => 'test-permission-3-'.time()]);

        return $this->list($this->url);
    }

    /**
     * test delete permission.
     */
    #[Test]
    public function test_delete_permission(): TestResponse
    {
        $permission = Permission::create(['name' => 'test-permission-'.time()]);

        return $this->destroy($this->url, $permission->id);
    }

    #[Test]
    public function test_structure(): void
    {
        Permission::create(['name' => 'test-permission-1-'.time()]);
        Permission::create(['name' => 'test-permission-2-'.time()]);
        $response = $this->withHeaders($this->getAuthHeaders())->json('GET', '/api/v1/permissions');
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
