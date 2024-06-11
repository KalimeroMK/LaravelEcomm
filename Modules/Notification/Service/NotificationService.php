<?php

namespace Modules\Notification\Service;

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

}
