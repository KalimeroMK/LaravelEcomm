<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;

class CreateMessageAction
{
    private MessageRepository $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(MessageDTO $dto): Message
    {
        /** @var Message $message */
        $message = $this->repository->create([
            'name' => $dto->name,
            'subject' => $dto->subject,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'message' => $dto->message,
            'read_at' => $dto->read_at,
        ]);

        if (! empty($dto->photo) && is_file($dto->photo)) {
            $message->clearMediaCollection('photo');

            $message->addMedia($dto->photo)
                ->preservingOriginal()
                ->toMediaCollection('photo');
        }

        return $message;
    }
}
