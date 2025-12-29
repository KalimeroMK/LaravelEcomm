<?php

declare(strict_types=1);

namespace Modules\Tenant\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Tenant\Repository\TenantRepository;

readonly class FindTenantAction
{
    public function __construct(private TenantRepository $repository) {}

    public function execute(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }
}
