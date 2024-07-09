<?php

namespace Modules\User\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Modules\Role\Models\Role;
use Modules\User\Models\User;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create';
    protected $description = 'Create a new user and assign a role';

    public function handle(): void
    {
        $name = $this->ask('What is the user\'s name?');
        $email = $this->ask('What is the user\'s email?');
        $password = $this->secret('What is the user\'s password?');

        // Validate input
        $validator = Validator::make(compact('name', 'email', 'password'), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('User not created. See error messages below:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return;
        }

        // Create user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),  // Securely hash the password
        ]);

        // Get available roles
        $roles = Role::pluck('name')->toArray();
        $roleName = $this->choice('Which role do you want to assign to the user?', $roles);

        // Assign role
        $user->assignRole($roleName);

        // Output success information
        $this->info('User created successfully.');
    }
}
