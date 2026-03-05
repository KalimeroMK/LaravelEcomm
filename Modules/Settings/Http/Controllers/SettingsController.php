<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Settings\Actions\CreateDefaultSettingsAction;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateSettingsAction;
use Modules\Settings\Http\Requests\Update;
use Modules\Settings\Models\Setting;

/**
 * Settings Controller
 *
 * Handles settings management with a SINGLE record approach.
 * Settings are always present - they can only be edited, never deleted.
 */
final class SettingsController extends Controller
{
    public function __construct(
        private GetSettingsAction $getSettingsAction,
        private FindSettingAction $findSettingAction,
        private UpdateSettingsAction $updateSettingsAction,
        private CreateDefaultSettingsAction $createDefaultSettingsAction
    ) {}

    /**
     * Display settings edit form.
     * If no settings exist, creates default ones automatically.
     */
    public function index(): View
    {
        $this->authorize('viewAny', Setting::class);

        // Get or create settings (ensures settings always exist)
        $settings = $this->getSettingsAction->execute();

        if (empty($settings)) {
            $setting = $this->createDefaultSettingsAction->execute();
            $settings = $this->getSettingsAction->execute();
        }

        return view('settings::edit', compact('settings'));
    }

    /**
     * Update settings.
     * Only updates existing settings - cannot create new ones from here.
     */
    public function update(Update $request): RedirectResponse
    {
        $this->authorize('update', Setting::class);

        $setting = Setting::first();

        // If for some reason settings don't exist, create them first
        if (! $setting) {
            $setting = $this->createDefaultSettingsAction->execute();
        }

        $this->updateSettingsAction->execute($setting->id, $request->validated());

        return redirect()
            ->back()
            ->with('success', 'Settings updated successfully.');
    }
}
