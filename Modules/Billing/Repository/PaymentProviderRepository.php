<?php

declare(strict_types=1);

namespace Modules\Billing\Repository;

use Modules\Billing\Models\PaymentProvider;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;

class PaymentProviderRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(PaymentProvider::class);
    }
}
