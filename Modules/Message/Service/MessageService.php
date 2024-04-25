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
     * Show details of an attribute.
     *
     * @param  int  $id  The attribute ID.
     * @return mixed
     */
    public function show(int $id): mixed
    {
        $message = $this->message_repository->findById($id);
        $message->read_at = Carbon::now();
        $message->save();

        return $message;
    }


    /**
     * @param  int  $id
     *
     * @return void
     */
    public function destroy(int $id): void
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
     * Store a new attribute.
     *
     * @param  array<string, mixed>  $data  The data to create the attribute.
     * @return mixed
     */
    public function store(array $data): mixed
    {
        return $this->message_repository->create($data);
    }

    /**
     * Update the specified coupon.
     * @param  int  $id  The ID of the coupon to update.
     * @param  array<string, mixed>  $data  Data to update the coupon.
     * @return bool Result of the update operation.
     */
    public function update(int $id, array $data): mixed
    {
        return $this->message_repository->update($id, $data);
    }
}
