<?php

namespace Modules\Billing\Repository;

use Modules\Billing\Models\PaymentProvider;
use Modules\Core\Repositories\Repository;

class PaymentProviderRepository extends Repository
{
    /**
     * @var string
     */
    public $model = PaymentProvider::class;
}
