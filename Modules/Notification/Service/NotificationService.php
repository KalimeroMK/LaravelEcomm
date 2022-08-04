<?php

namespace Modules\Notification\Service;

use Exception;
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
        try {
            $this->notification_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        try {
            return $this->notification_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return mixed|string
     */
    public function getById($id): mixed
    {
        try {
            return $this->notification_repository->getById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}