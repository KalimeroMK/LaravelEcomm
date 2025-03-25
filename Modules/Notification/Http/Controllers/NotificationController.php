<?php

declare(strict_types=1);

namespace Modules\Notification\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Notification\Service\NotificationService;

class NotificationController extends Controller
{
    private NotificationService $notification_service;

    public function __construct(NotificationService $notification_service)
    {
        $this->notification_service = $notification_service;
        $this->middleware('permission:notification-list', ['only' => ['index']]);
        $this->middleware('permission:notification-show', ['only' => ['show']]);
        $this->middleware('permission:notification-delete', ['only' => ['destroy']]);
    }

    public function index(): View|Factory|Application
    {
        return view('notification::index', ['notifications' => $this->notification_service->getAll()]);
    }

    /**
     * @return Application|Factory|View|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show(int $id)
    {
        $notification = $this->notification_service->findById($id);

        return view('notification::show', ['notification' => $notification]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->notification_service->delete($id);

        return redirect()->back();
    }
}
