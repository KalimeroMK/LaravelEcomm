<?php

namespace Modules\Billing\Models;

use Modules\Core\Models\Core;

class PaymentProvider extends Core
{
    protected $table = 'payment_provider';

    protected $fillable
        = [
            'name',
            'public_key',
            'secret_key',
            'status',
        ];
}
