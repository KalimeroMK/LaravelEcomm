<?php

namespace Modules\Tenant\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
            'domain' => [
                'required', 'string', 'max:255', 'unique:owner.tenants',
                'regex:/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9][a-z0-9-]{0,61}[a-z0-9]$/',
            ],
            'database' => ['required', 'string', 'max:255', 'unique:owner.tenants'],
        ]);

        if ($validator->fails()) {
            $this->error('Tenant not created. See error messages below:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return;
        }

        // Check if the database already exists
        if ($this->databaseExists($database)) {
            $this->error("Database {$database} already exists. Tenant not created.");

            return;
        }
        // Create the database
        $this->createDatabase($database);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $name,
            'domain' => $domain,
            'database' => $database,
        ]);

        $this->info("Tenant {$tenant->name} created successfully with domain {$tenant->domain} and database {$tenant->database}.");
    }

    /**
     * Check if a database exists.
     */
    protected function databaseExists(string $database): bool
    {
        try {
            $ownerConnection = 'owner';
            $this->info("Using connection: {$ownerConnection}");
            $query = "SHOW DATABASES LIKE '{$database}'";
            $databaseExists = DB::connection($ownerConnection)->select($query);

            return ! empty($databaseExists);
        } catch (Exception $e) {
            $this->error('Error checking database existence: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Create a new database.
     */
    protected function createDatabase(string $database): void
    {
        try {
            $ownerConnection = 'owner';
            $this->info("Creating database using connection: {$ownerConnection}");
            DB::connection($ownerConnection)->statement("CREATE DATABASE `{$database}`");
            $this->info("Database {$database} created successfully.");
        } catch (Exception $e) {
            $this->error('Error creating database: '.$e->getMessage());
        }
    }
}
