<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Http\Requests\Update;
use Modules\Settings\Models\Setting;
use Modules\Settings\Service\SettingsService;

class SettingsController extends Controller
{
    private SettingsService $settings_service;

    public function __construct(SettingsService $settings_service)
    {
        $this->settings_service = $settings_service;
        $this->authorizeResource(Setting::class, 'setting');
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('settings::edit', ['settings' => $this->settings_service->getData()]);
    }

    public function Update(Update $request, Setting $setting): RedirectResponse
    {
        $this->settings_service->update($setting->id, $request->validated());

        return redirect()->back();
    }
}
