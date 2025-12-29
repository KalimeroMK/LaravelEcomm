<?php

declare(strict_types=1);

namespace Modules\Tenant\Actions;

use Modules\Tenant\DTOs\TenantDTO;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Repository\TenantRepository;

readonly class UpdateTenantAction
{
    public function __construct(private TenantRepository $repository) {}

    public function execute(int $id, TenantDTO $dto): Tenant
    {
        $tenant = $this->repository->findOrFail($id);
        $tenant->update($dto->toArray());

        return $tenant->fresh();
    }
}
