<?php

declare(strict_types=1);

namespace Modules\Tenant\Actions;

use Modules\Tenant\DTOs\TenantDTO;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Repository\TenantRepository;

readonly class CreateTenantAction
{
    public function __construct(private TenantRepository $repository) {}

    public function execute(TenantDTO $dto): Tenant
    {
        return $this->repository->create($dto->toArray());
    }
}
