<?php

declare(strict_types=1);

namespace Modules\Google2fa\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Google2fa\Actions\Get2FASettingsAction;
use Modules\Google2fa\Actions\Update2FASettingsAction;
use Modules\Google2fa\Models\Google2faSetting;
use Modules\Role\Models\Role;

class Google2faSettingsController extends CoreController
{
    public function __construct(
        private readonly Get2FASettingsAction $get2FASettingsAction,
        private readonly Update2FASettingsAction $update2FASettingsAction
    ) {}

    /**
     * Get 2FA settings.
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Google2faSetting::class);

        $settings = $this->get2FASettingsAction->execute();
        $roles = Role::all();

        return $this
            ->setMessage('2FA settings retrieved successfully.')
            ->respond([
                'settings' => $settings,
                'roles' => $roles,
            ]);
    }

    /**
     * Update 2FA settings.
     */
    public function update(Request $request): JsonResponse
    {
        $settings = $this->get2FASettingsAction->execute();
        $this->authorize('update', $settings);

        $validated = $request->validate([
            'enforce_for_admins' => 'boolean',
            'enforce_for_users' => 'boolean',
            'enforced_roles' => 'nullable|array',
            'enforced_roles.*' => 'string|exists:roles,name',
            'recovery_codes_count' => 'required|integer|min:5|max:20',
            'require_backup_codes' => 'boolean',
        ]);

        $updatedSettings = $this->update2FASettingsAction->execute($validated);

        return $this
            ->setMessage('2FA settings updated successfully.')
            ->respond($updatedSettings);
    }
}
