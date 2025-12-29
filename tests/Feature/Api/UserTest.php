<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\User\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\Feature\Api\Traits\AuthenticatedBaseTestTrait;
use Tests\TestCase;

class UserTest extends TestCase
{
    use AuthenticatedBaseTestTrait;
    use RefreshDatabase;
    use WithFaker;

    public string $url = '/api/v1/users';

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Create super-admin user with permissions
        $this->user = User::factory()->create();
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin']);

        // Create and assign user permissions
        $permissions = [
            'user-list',
            'user-create',
            'user-update',
            'user-delete',
        ];

        foreach ($permissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
            $superAdminRole->givePermissionTo($perm);
        }

        $this->user->assignRole($superAdminRole);

        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    #[Test]
    public function test_create_user(): void
    {
        Storage::fake('public');

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [],
        ];

        $response = $this->create($this->url, $data);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'status',
            ],
        ]);
    }

    #[Test]
    public function test_get_all_users(): void
    {
        User::factory()->count(3)->create();

        $response = $this->list($this->url);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'status',
                ],
            ],
        ]);
    }

    #[Test]
    public function test_find_user(): void
    {
        $testUser = User::factory()->create();

        $response = $this->show($this->url, $testUser->id);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'status',
            ],
        ]);
    }

    #[Test]
    public function test_update_user(): void
    {
        $testUser = User::factory()->create();
        $data = [
            'name' => 'Updated Name',
            'email' => $testUser->email, // Keep same email
        ];

        $response = $this->updatePUT($this->url, $data, $testUser->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);

        $testUser->refresh();
        expect($testUser->name)->toBe('Updated Name');
    }

    #[Test]
    public function test_delete_user(): void
    {
        $testUser = User::factory()->create();

        $response = $this->destroy($this->url, $testUser->id);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('users', ['id' => $testUser->id]);
    }

    #[Test]
    public function test_create_user_with_roles(): void
    {
        $role = Role::firstOrCreate(['name' => 'test-role']);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => [$role->id],
        ];

        $response = $this->create($this->url, $data);
        $response->assertStatus(200);

        $user = User::where('email', $data['email'])->first();
        expect($user->roles)->toHaveCount(1);
        expect($user->roles->first()->id)->toBe($role->id);
    }

    #[Test]
    public function test_structure(): void
    {
        User::factory()->count(2)->create();

        $response = $this->withHeaders($this->getAuthHeaders())
            ->json('GET', $this->url);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'status',
                ],
            ],
        ]);
    }
}

