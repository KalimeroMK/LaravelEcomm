<?php

declare(strict_types=1);

namespace Modules\Notification\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Notification\DTOs\NotificationDTO;
use Modules\Notification\Repository\NotificationRepository;

class UpdateNotificationAction
{
    private NotificationRepository $repository;

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(NotificationDTO $dto): Model
    {
        $notification = $this->repository->findById($dto->id);
        $notification->update([
            'type' => $dto->type,
            'notifiable_type' => $dto->notifiable_type,
            'notifiable_id' => $dto->notifiable_id,
            'data' => $dto->data,
            'read_at' => $dto->read_at,
        ]);
        return $notification;
    }
}
