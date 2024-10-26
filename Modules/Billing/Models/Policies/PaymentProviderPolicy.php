<?php

namespace Modules\Billing\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Billing\Models\PaymentProvider;
use Modules\User\Models\User;

class PaymentProviderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('payment-provider-list');
    }

    public function view(User $user, PaymentProvider $paymentProvider): bool
    {
        return $user->can('payment-provider-list');
    }

    public function create(User $user): bool
    {
        return $user->can('payment-provider-create');
    }

    public function update(User $user, PaymentProvider $paymentProvider): bool
    {
        return $user->can('payment-provider-update');
    }
    
}
