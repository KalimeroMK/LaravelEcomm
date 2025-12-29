<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Invoice;

use Illuminate\Support\Collection;
use Modules\Billing\Repository\InvoiceRepository;

readonly class GetAllInvoicesAction
{
    public function __construct(private InvoiceRepository $repository) {}

    public function execute(?int $userId = null): Collection
    {
        if ($userId) {
            return $this->repository->findByUser($userId);
        }

        return $this->repository->findAll();
    }
}
