<?php

declare(strict_types=1);

namespace Modules\Billing\Actions\Payment;

use Modules\Billing\Repository\PaymentRepository;

readonly class GetPaymentAnalyticsAction
{
    public function __construct(private PaymentRepository $repository) {}

    public function execute(): array
    {
        return $this->repository->getAnalytics();
    }
}
