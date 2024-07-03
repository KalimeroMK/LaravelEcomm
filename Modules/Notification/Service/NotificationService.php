<?php

namespace Modules\Notification\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Modules\Core\Service\CoreService;
use Modules\Notification\Repository\NotificationRepository;

class NotificationService extends CoreService
{
    private NotificationRepository $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        parent::__construct($notification_repository);
        $this->notification_repository = $notification_repository;
    }


    public function findById(int $id): ?Model
    {
        $this->notification_repository->update($id, ['read_at' => Carbon::now()]);
        return parent::findById($id);
    }

}
