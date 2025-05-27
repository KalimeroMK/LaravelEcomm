<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;

class UpdateMessageAction
{
    private MessageRepository $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(MessageDTO $dto): Message
    {
        $message = $this->repository->findById($dto->id);
        /** @var Message $message */
        $message->update([
            'name' => $dto->name,
            'subject' => $dto->subject,
            'message' => $dto->message,
            'email' => $dto->email,
            'phone' => $dto->phone, // optional
        ]);

        if (! empty($dto->images) && is_array($dto->images)) {
            $message->clearMediaCollection('messages');
            foreach ($dto->images as $image) {
                $message->addMedia($image)
                    ->preservingOriginal()
                    ->toMediaCollection('messages');
            }
        }

        return $message;
    }
}
