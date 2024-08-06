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
    }

    public function view(User $user, PaymentProvider $paymentProvider): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, PaymentProvider $paymentProvider): bool
    {
    }

    public function delete(User $user, PaymentProvider $paymentProvider): bool
    {
    }

    public function restore(User $user, PaymentProvider $paymentProvider): bool
    {
    }

    public function forceDelete(User $user, PaymentProvider $paymentProvider): bool
    {
    }
}
