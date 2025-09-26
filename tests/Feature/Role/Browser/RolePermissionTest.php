<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Models\User;

require_once __DIR__ . '/../../../TestHelpers.php';
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = createSuperAdminUser();
});

test('admin can view roles list', function () {
    Role::create(['name' => 'customer']);
    Role::create(['name' => 'editor']);

    $response = $this->actingAs($this->admin)
        ->get('/admin/roles');

    $response->assertStatus(200);
    $response->assertSee('Roles');
});

test('admin can create role', function () {
    $roleData = [
        'name' => 'manager',
        'guard_name' => 'web',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/roles', $roleData);

    $response->assertRedirect();
    $this->assertDatabaseHas('roles', [
        'name' => 'manager',
    ]);
});

test('admin can assign permissions to role', function () {
    $role = Role::create(['name' => 'editor']);
    $permission = Permission::create(['name' => 'edit-posts']);

    $response = $this->actingAs($this->admin)
        ->post("/admin/roles/{$role->id}/permissions", [
            'permissions' => [$permission->id],
        ]);

    $response->assertRedirect();
    expect($role->hasPermissionTo('edit-posts'))->toBeTrue();
});

test('admin can assign role to user', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'customer']);

    $response = $this->actingAs($this->admin)
        ->post("/admin/users/{$user->id}/roles", [
            'roles' => [$role->id],
        ]);

    $response->assertRedirect();
    expect($user->hasRole('customer'))->toBeTrue();
});

test('admin can view permissions list', function () {
    Permission::create(['name' => 'create-posts']);
    Permission::create(['name' => 'edit-posts']);

    $response = $this->actingAs($this->admin)
        ->get('/admin/permissions');

    $response->assertStatus(200);
    $response->assertSee('Permissions');
});

test('admin can create permission', function () {
    $permissionData = [
        'name' => 'delete-posts',
        'guard_name' => 'web',
    ];

    $response = $this->actingAs($this->admin)
        ->post('/admin/permissions', $permissionData);

    $response->assertRedirect();
    $this->assertDatabaseHas('permissions', [
        'name' => 'delete-posts',
    ]);
});

test('user with role can access protected routes', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'editor']);
    $permission = Permission::create(['name' => 'edit-posts']);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    $response = $this->actingAs($user)
        ->get('/admin/posts');

    $response->assertStatus(200);
});

test('user without permission cannot access protected routes', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/admin/posts');

    $response->assertStatus(403);
});
