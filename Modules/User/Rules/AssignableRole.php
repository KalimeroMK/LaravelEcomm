<?php

declare(strict_types=1);

namespace Modules\User\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Modules\Role\Models\Role;

/**
 * Blocks a user from granting a role that outranks their own.
 *
 * Only a super-admin may hand out the super-admin role. Without this, any
 * account holding `user-update` could mint a new super-admin and take over the
 * application - either directly or by creating a throwaway account first.
 *
 * This is defence in depth. The primary control is UserPolicy::assignRoles(),
 * which decides *whether* a user may touch role sets at all; this rule narrows
 * *which* roles they may grant once they can.
 */
class AssignableRole implements ValidationRule
{
    /**
     * Roles that only a super-admin may assign.
     *
     * @var list<string>
     */
    private const PROTECTED_ROLES = ['super-admin'];

    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $actor = Auth::user();

        if ($actor !== null && $actor->hasRole('super-admin')) {
            return;
        }

        $roleName = Role::query()->whereKey($value)->value('name');

        if ($roleName !== null && in_array($roleName, self::PROTECTED_ROLES, true)) {
            $fail(sprintf('You are not allowed to assign the "%s" role.', $roleName));
        }
    }
}
