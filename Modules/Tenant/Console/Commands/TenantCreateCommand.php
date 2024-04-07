<?php

namespace Modules\Tenant\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Modules\Tenant\Models\Tenant;

class TenantCreateCommand extends Command
{
    protected $signature = 'tenants:create';
    protected $description = 'Create a new tenant with a unique domain and database';

    public function handle(): void
    {
        $name = $this->ask('What is the tenant\'s name?');
        $domain = $this->ask('What is the tenant\'s domain?');
        $database = $this->ask('What is the tenant\'s database name?');

        // Validate input
        $validator = Validator::make(compact('name', 'domain', 'database'), [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:owner.tenants'],
            'database' => ['required', 'string', 'max:255', 'unique:owner.tenants'],
        ]);


        if ($validator->fails()) {
            $this->error('Tenant not created. See error messages below:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return;
        }

        // Create tenant
        $tenant = Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'database' => $database,
        ]);

        $this->info("Tenant {$tenant->name} created successfully with domain {$tenant->domain} and database {$tenant->database}.");
    }
}
