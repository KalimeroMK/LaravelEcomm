<?php

declare(strict_types=1);

namespace Modules\Tenant\Service;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Service\CoreService;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Repository\TenantRepository;

class TenantService extends CoreService
{
    public function __construct(private readonly TenantRepository $repository) {}

    public function getAll(): Collection
    {
        return $this->repository->all();
    }

    public function create(array $data): Tenant
    {
        /** @var Tenant */
        return $this->repository->create($data);
    }

    public function findById(int $id): ?Tenant
    {
        /** @var Tenant|null */
        return $this->repository->find($id);
    }

    public function update(int $id, array $data): bool
    {
        if (! $this->repository->exists($id)) {
            return false;
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        if (! $this->repository->exists($id)) {
            return false;
        }

        return $this->repository->delete($id);
    }
}
