<?php

declare(strict_types=1);

namespace Modules\Settings\Models\Polices;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Settings\Models\Setting;
use Modules\User\Models\User;

/**
 * Settings Policy
 *
 * IMPORTANT: Settings use SINGLE RECORD approach.
 * - Anyone can view settings
 * - Only admins can update settings
 * - NO ONE can delete settings (prevents application breakage)
 * - NO ONE can create new settings (only one record allowed)
 */
class SettingsPolicy
{
    use HandlesAuthorization;

    /**
     * Anyone can view settings
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Anyone can view a specific setting
     */
    public function view(User $user, Setting $setting): bool
    {
        return true;
    }

    /**
     * NO ONE can create new settings - only ONE record allowed
     * This always returns false to enforce single record pattern
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Only admins can update settings
     */
    public function update(User $user, Setting $setting): bool
    {
        return $user->hasAnyRole(['admin', 'super-admin']);
    }

    /**
     * NO ONE can delete settings - this prevents application breakage
     * Settings are required for the application to function
     */
    public function delete(User $user, Setting $setting): bool
    {
        // Settings cannot be deleted to prevent application breakage
        return false;
    }

    /**
     * NO ONE can restore settings (soft deletes not used)
     */
    public function restore(User $user, Setting $setting): bool
    {
        return false;
    }

    /**
     * NO ONE can force delete settings
     */
    public function forceDelete(User $user, Setting $setting): bool
    {
        // Settings cannot be force deleted
        return false;
    }
}
