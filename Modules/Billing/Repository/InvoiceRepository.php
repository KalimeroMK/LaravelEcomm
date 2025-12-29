<?php

declare(strict_types=1);

namespace Modules\Billing\Repository;

use Illuminate\Support\Collection;
use Modules\Billing\Models\Invoice;
use Modules\Core\Repositories\EloquentRepository;

class InvoiceRepository extends EloquentRepository
{
    public function __construct()
    {
        parent::__construct(Invoice::class);
    }

    public function findAll(): Collection
    {
        return Invoice::with(['user', 'order'])->get();
    }

    public function findByUser(int $userId): Collection
    {
        return Invoice::where('user_id', $userId)
            ->with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?Invoice
    {
        return Invoice::where('invoice_number', $invoiceNumber)
            ->with(['user', 'order', 'payments'])
            ->first();
    }
}
