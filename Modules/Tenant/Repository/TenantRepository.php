<?php

declare(strict_types=1);

namespace Modules\Tenant\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Tenant\Models\Tenant;

class TenantRepository extends Repository
{
    /**
     * Get the model instance.
     */
    public function getModel(): Tenant
    {
        return new Tenant;
    }
}
