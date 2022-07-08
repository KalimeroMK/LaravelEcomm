<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Modules\Notification\Service\NotificationService;

class NotificationController extends Controller
{
    private NotificationService $notification_service;
    
    public function __construct(NotificationService $notification_service)
    {
        $this->notification_service = $notification_service;
    }
    
    /**
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return $this->notification_service->index();
    }
    
    /**
     * @param  Request  $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function show(Request $request): Redirector|RedirectResponse|Application
    {
        return $this->notification_service->show($request);
    }
    
    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        return $this->notification_service->delete($id);
    }
}
