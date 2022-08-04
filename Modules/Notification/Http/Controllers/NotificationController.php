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

class  NotificationController extends Controller
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
        return view('notification::index', ['notifications' => $this->notification_service->getAll()]);
    }
    
    /**
     * @param  Request  $request
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function show(Request $request): Redirector|RedirectResponse|Application
    {
        $notification = $this->notification_service->getById($request->id);
        $data         = json_decode($notification->data);
        
        return redirect($data->actionURL);
    }
    
    /**
     * @param $id
     *
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        $this->notification_service->destroy($id);
        
        return redirect()->back();
    }
}
