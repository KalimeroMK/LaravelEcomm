<?php

namespace Modules\Message\Service;

use Carbon\Carbon;
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
        $message = $this->message_repository->findById($id);
        if (
            true
        ) {
            $message->read_at = Carbon::now();
            $message->save();
        }
        
        return $message;
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->message_repository->delete($id);
    }
    
    /**
     * @return array
     */
    public function index(): array
    {
        return $this->message_repository->findAll();
    }
}