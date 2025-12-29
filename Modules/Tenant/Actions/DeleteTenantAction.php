<?php

declare(strict_types=1);

namespace Modules\Tenant\Actions;

use Modules\Tenant\Repository\TenantRepository;

readonly class DeleteTenantAction
{
    public function __construct(private TenantRepository $repository) {}

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
