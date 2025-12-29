<?php

declare(strict_types=1);

namespace Modules\Billing\Repository;

use Illuminate\Support\Collection;
use Modules\Billing\Models\Payment;
use Modules\Core\Repositories\EloquentRepository;

class PaymentRepository extends EloquentRepository
{
    public function __construct()
    {
        parent::__construct(Payment::class);
    }

    public function findAll(): Collection
    {
        return Payment::with(['user', 'order', 'invoice'])->get();
    }

    public function findByUser(int $userId): Collection
    {
        return Payment::where('user_id', $userId)
            ->with(['user', 'order', 'invoice'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findByTransactionId(string $transactionId): ?Payment
    {
        return Payment::where('transaction_id', $transactionId)
            ->with(['user', 'order', 'invoice'])
            ->first();
    }

    public function getAnalytics(): array
    {
        $totalPayments = Payment::where('status', 'completed')->sum('amount');
        $totalCount = Payment::where('status', 'completed')->count();
        $pendingCount = Payment::where('status', 'pending')->count();
        $failedCount = Payment::where('status', 'failed')->count();

        return [
            'total_amount' => (float) $totalPayments,
            'total_count' => $totalCount,
            'pending_count' => $pendingCount,
            'failed_count' => $failedCount,
            'success_rate' => $totalCount > 0 ? ($totalCount / ($totalCount + $failedCount)) * 100 : 0,
        ];
    }
}
