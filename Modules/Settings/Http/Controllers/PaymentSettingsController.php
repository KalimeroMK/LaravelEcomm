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
use Modules\Settings\Actions\UpdatePaymentSettingsAction;
use Modules\Settings\Models\Setting;

class PaymentSettingsController extends Controller
{
    public function __construct(
        private readonly GetSettingsAction $getSettingsAction,
        private readonly FindSettingAction $findSettingAction,
        private readonly UpdatePaymentSettingsAction $updatePaymentSettingsAction
    ) {
        // Authorization is handled explicitly in each method
    }

    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', Setting::class);
        $settings = $this->findSettingAction->execute();

        return view('settings::payment.index', [
            'settings' => $settings,
            'paymentSettings' => $settings?->payment_settings ?? [],
        ]);
    }

    public function update(Request $request, Setting $setting): RedirectResponse
    {
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

        $this->updatePaymentSettingsAction->execute($setting, $validated);

        return redirect()->route('settings.payment.index')->with('success', 'Payment settings updated successfully');
    }
}
