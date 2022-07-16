<?php

namespace Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Http\Requests\Update;
use Modules\Settings\Service\SettingsService;

class SettingsController extends Controller
{
    private SettingsService $settings_service;
    
    public function __construct(SettingsService $settings_service)
    {
        $this->settings_service = $settings_service;
    }
    
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('admin::setting', ['data' => $this->settings_service->index()]);
    }
    
    /**
     * @param  Update  $request
     *
     * @return RedirectResponse
     */
    public function Update(Update $request): RedirectResponse
    {
        $this->settings_service->update($request);
        
        return redirect()->route('admin');
    }
}
