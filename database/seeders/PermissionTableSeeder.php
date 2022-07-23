<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        
        // create permissions
        $permissions = [
            'review-list',
            'review-create',
            'review-edit',
            'review-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'shipping-list',
            'shipping-create',
            'shipping-edit',
            'shipping-delete',
            'comments-list',
            'comments-create',
            'comments-edit',
            'comments-delete',
            'casys-update',
            'coupon-list',
            'coupon-create',
            'coupon-edit',
            'coupon-delete',
            'brand-list',
            'brand-create',
            'brand-edit',
            'brand-delete',
            'banner-list',
            'banner-create',
            'banner-edit',
            'banner-delete',
            'settings-list',
            'settings-create',
            'settings-edit',
            'settings-delete',
            'categories-list',
            'categories-create',
            'categories-edit',
            'categories-delete',
            'tags-list',
            'tags-create',
            'tags-edit',
            'tags-delete',
            'postCategory-list',
            'postCategory-create',
            'postCategory-edit',
            'postCategory-delete',
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'order-list',
            'order-create',
            'order-edit',
            'order-delete',
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'manager']);
        $role1->givePermissionTo([
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
            'order-list',
            'order-create',
            'order-edit',
            'coupon-list',
            'coupon-create',
            'coupon-edit',
            'coupon-delete',
            'brand-list',
            'brand-create',
            'brand-edit',
            'brand-delete',
            'banner-list',
            'banner-create',
            'banner-edit',
            'banner-delete',
            'user-list',
            'user-edit',
        ]);
        
        $role2 = Role::create(['name' => 'client']);
        $role2->givePermissionTo([
            'order-list',
            'order-create',
            'order-edit',
            'order-delete',
            'comments-list',
            'comments-create',
            'comments-edit',
            'comments-delete',
            'review-list',
            'review-create',
            'review-edit',
            'review-delete',
            'user-list',
            'user-edit',
        ]);
        
        $role3 = Role::create(['name' => 'super-admin']);
        $role3->givePermissionTo(Permission::all());
        
        // create demo users
        $user = User::factory()->create([
            'name'  => 'Example User',
            'email' => 'manager@mail.com',
        ]);
        $user->assignRole($role1);
        
        $user = User::factory()->create([
            'name'  => 'Example client User',
            'email' => 'client@mail.com',
        ]);
        $user->assignRole($role2);
        
        $user = User::factory()->create([
            'name'  => 'Example Super-Admin User',
            'email' => 'superadmin@mail.com',
        ]);
        $user->assignRole($role3);
    }
}
