<?php

namespace Modules\Message\Service;

use Carbon\Carbon;
use Modules\Message\Repository\MessageRepository;

class MessageService
{
    public MessageRepository $message_repository;

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
     * @param $id
     *
     * @return string|void
     */
    public function destroy($id)
    {
            $this->message_repository->delete($id);
    }

    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
            return $this->message_repository->findAll();
    }

    /**
     * @param $data
     *
     * @return mixed
     */
    public function store($data): mixed
    {
            return $this->message_repository->create($data);
    }

    /**
     * @param $id
     * @param $data
     *
     * @return mixed|string
     */
    public function update($id, $data): mixed
    {
            return $this->message_repository->update($id, $data);
    }
}
