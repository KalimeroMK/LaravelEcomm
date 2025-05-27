<?php

declare(strict_types=1);

namespace Modules\Notification\Actions;

use Modules\Notification\DTOs\NotificationDTO;
use Modules\Notification\Models\Notification;
use Modules\Notification\Repository\NotificationRepository;

class CreateNotificationAction
{
    private NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(NotificationDTO $dto): Notification
    {
        return $this->repository->create([
            'type' => $dto->type,
            'notifiable_type' => $dto->notifiable_type,
            'notifiable_id' => $dto->notifiable_id,
            'data' => $dto->data,
            'read_at' => $dto->read_at,
        ]);
    }
}
