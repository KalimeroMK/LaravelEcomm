<?php

declare(strict_types=1);

namespace Modules\Billing\Models\Policies;

use Modules\Billing\Models\Invoice;
use Modules\User\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']) || $user->id === $invoice->user_id;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }
}
