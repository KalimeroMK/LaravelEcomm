<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateEmailSettingsAction;
use Modules\Settings\Models\Setting;

class EmailSettingsController extends CoreController
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdateEmailSettingsAction $updateEmailSettingsAction
    ) {}

    /**
     * Get email settings
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
            ->setMessage('Email settings retrieved successfully.')
            ->respond([
                'settings' => $setting,
                'email_settings' => $setting->email_settings ?? [],
            ]);
    }

    /**
     * Update email settings
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
            'mail_driver' => 'nullable|in:smtp,sendmail,mailgun,ses,postmark,log',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|in:tls,ssl',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'mail_reply_to' => 'nullable|email|max:255',
        ]);

        $updatedSetting = $this->updateEmailSettingsAction->execute($setting, $validated);

        return $this
            ->setMessage('Email settings updated successfully.')
            ->respond([
                'settings' => $updatedSetting,
                'email_settings' => $updatedSetting->email_settings,
            ]);
    }
}
