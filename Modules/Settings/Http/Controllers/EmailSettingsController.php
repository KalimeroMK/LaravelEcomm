<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Settings\Actions\FindSettingAction;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateEmailSettingsAction;
use Modules\Settings\Models\Setting;

class EmailSettingsController extends Controller
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdateEmailSettingsAction $updateEmailSettingsAction
    ) {
        // Authorization is handled explicitly in each method
    }

    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', Setting::class);
        $settings = $this->findSettingAction->execute();

        return view('settings::email.index', [
            'settings' => $settings,
            'emailSettings' => $settings?->email_settings ?? [],
        ]);
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
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

        $this->updateEmailSettingsAction->execute($setting, $validated);

        return redirect()->route('settings.email.index')->with('success', 'Email settings updated successfully');
    }
}
