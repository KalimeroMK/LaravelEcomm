<?php

namespace Modules\Tenant\Service;

use Modules\Core\Service\CoreService;
use Modules\Tenant\Repository\TenantRepository;

class TenantService extends CoreService
{
    public function __construct(TenantRepository $tenantRepository)
    {
        parent::__construct($tenantRepository);
    }

}