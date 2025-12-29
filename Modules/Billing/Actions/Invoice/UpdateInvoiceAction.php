<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Invoice;

use Modules\Billing\DTOs\InvoiceDTO;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Repository\InvoiceRepository;

readonly class UpdateInvoiceAction
{
    public function __construct(private InvoiceRepository $repository) {}

    public function execute(InvoiceDTO $dto): Invoice
    {
        $invoice = $this->repository->findById($dto->id);
        $invoice->update($dto->toArray());

        return $invoice;
    }
}
