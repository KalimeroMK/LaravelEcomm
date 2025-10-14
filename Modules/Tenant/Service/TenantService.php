<?php

declare(strict_types=1);

namespace Modules\Tenant\Service;

use Modules\Core\Service\CoreService;
use Modules\Tenant\Models\Tenant;

class TenantService extends CoreService
{
    public function __construct() {}

    /**
     * Get all tenants.
     */
    public function getAll()
    {
        return Tenant::all();
    }

    /**
     * Create a new tenant.
     */
    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    /**
     * Find tenant by ID.
     */
    public function findById(int $id): ?Tenant
    {
        return Tenant::find($id);
    }

    /**
     * Update tenant.
     */
    public function update(int $id, array $data): bool
    {
        $tenant = Tenant::find($id);
        if (! $tenant) {
            return false;
        }

        return $tenant->update($data);
    }

    /**
     * Delete tenant.
     */
    public function delete(int $id): bool
    {
        $tenant = Tenant::find($id);
        if (! $tenant) {
            return false;
        }

        return $tenant->delete();
    }
}
