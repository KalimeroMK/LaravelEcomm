<?php

declare(strict_types=1);

namespace Modules\Notification\Actions;

use Illuminate\Support\Collection;
use Modules\Notification\Repository\NotificationRepository;

class GetAllNotificationsAction
{
    private NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
