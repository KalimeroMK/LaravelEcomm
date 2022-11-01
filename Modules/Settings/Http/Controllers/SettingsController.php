<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Requests\Update;
use Modules\Settings\Models\Setting;
use Modules\Size\Service\SizesService;

class SettingsController extends Controller
{
    private SizesService $settings_service;
    
    public function __construct(SizesService $settings_service)
    {
        $this->settings_service = $settings_service;
        $this->middleware('permission:settings-list', ['only' => ['index']]);
        $this->middleware('permission:settings-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:settings-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:settings-delete', ['only' => ['destroy']]);
    }
    
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('settings::edit', ['settings' => $this->settings_service->index()]);
    }
    
    /**
     * @param  Update  $request
     * @param  Setting  $setting
     *
     * @return RedirectResponse
     */
    public function Update(Setting $setting, Update $request): RedirectResponse
    {
        $this->settings_service->update($setting->id, $request->validated());
        
        return redirect()->back();
    }
}
