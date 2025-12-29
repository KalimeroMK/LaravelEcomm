<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Payment;

use Illuminate\Support\Collection;
use Modules\Billing\Repository\PaymentRepository;

readonly class GetUserPaymentsAction
{
    public function __construct(private PaymentRepository $repository) {}

    public function execute(int $userId): Collection
    {
        return $this->repository->findByUser($userId);
    }
}
