<?php

declare(strict_types=1);

namespace Modules\Google2fa\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Google2fa\Actions\Get2FASettingsAction;
use Modules\Google2fa\Actions\Update2FASettingsAction;
use Modules\Google2fa\Models\Google2faSetting;
use Modules\Role\Models\Role;

class Google2faSettingsController extends Controller
{
    public function __construct(
        private readonly Get2FASettingsAction $get2FASettingsAction,
        private readonly Update2FASettingsAction $update2FASettingsAction
    ) {}

    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', Google2faSetting::class);

        $settings = $this->get2FASettingsAction->execute();
        $roles = Role::all();

        return view('google2fa::settings.index', [
            'settings' => $settings,
            'roles' => $roles,
        ]);
    }

    public function update(Request $request): RedirectResponse
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

        $this->update2FASettingsAction->execute($validated);

        return redirect()->route('admin.2fa.settings')->with('success', '2FA settings updated successfully.');
    }
}
