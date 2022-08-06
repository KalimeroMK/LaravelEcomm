<?php

namespace Modules\Message\Service;

use Carbon\Carbon;
use Exception;
use Modules\Message\Repository\MessageRepository;

class MessageService
{
    private MessageRepository $message_repository;
    
    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function show($id): mixed
    {
        try {
            $message = $this->message_repository->findById($id);
            if (
                true
            ) {
                $message->read_at = Carbon::now();
                $message->save();
            }
            
            return $message;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
        try {
            $this->message_repository->delete($id);
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
            return $this->message_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}