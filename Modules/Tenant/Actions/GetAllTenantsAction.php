<?php

declare(strict_types=1);

namespace Modules\Tenant\Actions;

use Illuminate\Support\Collection;
use Modules\Tenant\Repository\TenantRepository;

readonly class GetAllTenantsAction
{
    public function __construct(private TenantRepository $repository) {}

    public function execute(): Collection
    {
        return $this->repository->all();
    }
}
