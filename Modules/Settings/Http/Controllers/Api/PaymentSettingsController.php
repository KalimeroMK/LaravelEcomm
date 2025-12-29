<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdatePaymentSettingsAction;
use Modules\Settings\Models\Setting;

class PaymentSettingsController extends CoreController
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdatePaymentSettingsAction $updatePaymentSettingsAction
    ) {}

    /**
     * Get payment settings
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
            ->setMessage('Payment settings retrieved successfully.')
            ->respond([
                'settings' => $setting,
                'payment_settings' => $setting->payment_settings ?? [],
            ]);
    }

    /**
     * Update payment settings
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
            'stripe_enabled' => 'boolean',
            'stripe_public_key' => 'nullable|string|max:255',
            'stripe_secret_key' => 'nullable|string|max:255',
            'paypal_enabled' => 'boolean',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_client_secret' => 'nullable|string|max:255',
            'paypal_mode' => 'nullable|in:sandbox,live',
            'cod_enabled' => 'boolean',
            'bank_transfer_enabled' => 'boolean',
            'bank_account_details' => 'nullable|string',
        ]);

        $updatedSetting = $this->updatePaymentSettingsAction->execute($setting, $validated);

        return $this
            ->setMessage('Payment settings updated successfully.')
            ->respond([
                'settings' => $updatedSetting,
                'payment_settings' => $updatedSetting->payment_settings,
            ]);
    }
}
