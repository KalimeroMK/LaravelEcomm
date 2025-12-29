<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateSettingsAction;
use Modules\Settings\Http\Requests\Update;
use Modules\Settings\Models\Setting;

class SettingsController extends CoreController
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdateSettingsAction $updateSettingsAction
    ) {}

    /**
     * Get all settings
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Setting::class);
        $settings = $this->getSettingsAction->execute();

        return $this
            ->setMessage('Settings retrieved successfully.')
            ->respond($settings);
    }

    /**
     * Update settings
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $setting = $this->findSettingAction->execute($id);

        if (! $setting) {
            return $this
                ->setCode(404)
                ->setMessage('Settings not found.')
                ->respond(null);
        }

        $this->authorize('update', $setting);

        $updatedSetting = $this->updateSettingsAction->execute($id, $request->validated());

        return $this
            ->setMessage('Settings updated successfully.')
            ->respond($updatedSetting);
    }
}
