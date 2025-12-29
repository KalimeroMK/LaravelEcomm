<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateShippingSettingsAction;
use Modules\Settings\Models\Setting;

class ShippingSettingsController extends CoreController
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdateShippingSettingsAction $updateShippingSettingsAction
    ) {}

    /**
     * Get shipping settings
     */
    public function index(): JsonResponse
    {
        $this->authorize('viewAny', Setting::class);
        $setting = $this->findSettingAction->execute();

        if (! $setting) {
            return $this
                ->setCode(404)
                ->setMessage('Settings not found.')
                ->respond(null);
        }

        return $this
            ->setMessage('Shipping settings retrieved successfully.')
            ->respond([
                'settings' => $setting,
                'shipping_settings' => $setting->shipping_settings ?? [],
            ]);
    }

    /**
     * Update shipping settings
     */
    public function update(Request $request): JsonResponse
    {
        $setting = $this->findSettingAction->execute();

        if (! $setting) {
            return $this
                ->setCode(404)
                ->setMessage('Settings not found.')
                ->respond(null);
        }

        $this->authorize('update', $setting);

        $validated = $request->validate([
            'default_shipping_method' => 'nullable|string|max:255',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'flat_rate_shipping' => 'nullable|numeric|min:0',
            'shipping_zones' => 'nullable|array',
            'shipping_zones.*.name' => 'required|string|max:255',
            'shipping_zones.*.price' => 'required|numeric|min:0',
            'estimated_delivery_days' => 'nullable|integer|min:1',
        ]);

        $updatedSetting = $this->updateShippingSettingsAction->execute($setting, $validated);

        return $this
            ->setMessage('Shipping settings updated successfully.')
            ->respond([
                'settings' => $updatedSetting,
                'shipping_settings' => $updatedSetting->shipping_settings,
            ]);
    }
}
