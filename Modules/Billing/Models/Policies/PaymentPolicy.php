<?php

declare(strict_types=1);

namespace Modules\Billing\Models\Policies;

use Modules\Billing\Models\Payment;
use Modules\User\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $payment->user_id;
    }

    public function create(User $user): bool
    {
        return true; // Users can create payments
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
