<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Invoice;

use Illuminate\Support\Str;
use Modules\Billing\DTOs\InvoiceDTO;
use Modules\Billing\Models\Invoice;
use Modules\Billing\Repository\InvoiceRepository;

readonly class CreateInvoiceAction
{
    public function __construct(private InvoiceRepository $repository) {}

    public function execute(InvoiceDTO $dto): Invoice
    {
        $data = $dto->toArray();

        // Generate invoice number if not provided
        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = 'INV-'.mb_strtoupper(Str::random(10));
        }

        return $this->repository->create($data);
    }
}
