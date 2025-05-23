<?php

declare(strict_types=1);

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Settings\Actions\GetSettingsAction;
use Modules\Settings\Actions\UpdateSettingsAction;
use Modules\Settings\Http\Requests\Update;
use Modules\Settings\Models\Setting;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Setting::class, 'setting');
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $settingsDto = (new GetSettingsAction())->execute();

        return view('settings::edit', ['settings' => $settingsDto->settings]);
    }

    public function update(Update $request, Setting $setting): RedirectResponse
    {
        (new UpdateSettingsAction())->execute($setting->id, $request->validated());

        return redirect()->back();
    }
}
