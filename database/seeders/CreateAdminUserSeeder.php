<?php

    namespace Database\Seeders;

    use Illuminate\Database\Seeder;
    use Modules\User\Models\User;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;

    class CreateAdminUserSeeder extends Seeder
    {
        /**
         * Run the database seeders.
         *
         * @return void
         */

        public function run()
        {
            $user        = User::create([
                'name'     => 'Hardik Savani',
                'email'    => 'admin@gmail.com',
                'password' => bcrypt('123456'),

            ]);
            $role        = Role::create(['name' => 'Admin']);
            $permissions = Permission::pluck('id', 'id')->all();
            $role->syncPermissions($permissions);
            $user->assignRole([$role->id]);
        }
    }
