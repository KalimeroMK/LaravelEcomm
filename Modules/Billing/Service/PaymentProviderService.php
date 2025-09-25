<?php

declare(strict_types=1);

namespace Modules\Billing\Service;

use Modules\Billing\Repository\PaymentProviderRepository;
use Modules\Core\Service\CoreService;

class PaymentProviderService extends CoreService
{
    public PaymentProviderRepository $paymentProviderRepository;

    public function __construct(PaymentProviderRepository $paymentProviderRepository)
    {
        $this->paymentProviderRepository = $paymentProviderRepository;
    }
}
