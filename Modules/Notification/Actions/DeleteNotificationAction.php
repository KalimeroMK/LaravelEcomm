<?php

declare(strict_types=1);

namespace Modules\Notification\Actions;

use Modules\Notification\Repository\NotificationRepository;

class DeleteNotificationAction
{
    private NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        $this->repository->destroy($id);
    }
}
