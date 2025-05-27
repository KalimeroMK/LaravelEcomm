<?php

declare(strict_types=1);

namespace Modules\Notification\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Notification\Actions\DeleteNotificationAction;
use Modules\Notification\Actions\FindNotificationAction;
use Modules\Notification\Actions\GetAllNotificationsAction;
use Modules\Notification\Models\Notification;
use Modules\Notification\Repository\NotificationRepository;

class NotificationController extends CoreController
{
    public function __construct(
        private readonly GetAllNotificationsAction $getAllAction,
        private readonly DeleteNotificationAction $deleteAction,
        private readonly FindNotificationAction $findAction
    ) {
        // Removed permission middleware — using policies instead
    }

    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', Notification::class);

        $notifications = $this->getAllAction->execute();

        return view('notification::index', ['notifications' => $notifications]);
    }

    public function show(int $id): View|Factory|Application
    {
        $notification = $this->findAction->execute($id);
        $this->authorize('view', $notification);

        return view('notification::show', ['notification' => $notification]);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->authorizeFromRepo(NotificationRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return redirect()->back();
    }
}
