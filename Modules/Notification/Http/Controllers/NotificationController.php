<?php

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        $notification = $this->notification_service->getById($request->id);

        return view('notification::', compact('notification'));
    }

    /**
     * @param  int  $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->notification_service->destroy($id);

        return redirect()->back();
    }
}
