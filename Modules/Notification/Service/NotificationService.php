<?php

namespace Modules\Notification\Service;

use Modules\Notification\Repository\NotificationRepository;

class NotificationService
{
    private NotificationRepository $notification_repository;

    public function __construct(NotificationRepository $notification_repository)
    {
        $this->notification_repository = $notification_repository;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
            $this->notification_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
            return $this->notification_repository->findAll();
    }

    /**
     * @return mixed|string
     */
    public function getById($id): mixed
    {
            return $this->notification_repository->getById($id);
    }
}
