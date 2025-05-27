<?php

declare(strict_types=1);

namespace Modules\Notification\Actions;

use Modules\Notification\Models\Notification;
use Modules\Notification\Repository\NotificationRepository;

class FindNotificationAction
{
    private NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): ?Notification
    {
        return $this->repository->getById($id);
    }
}
