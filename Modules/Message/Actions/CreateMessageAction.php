<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;

readonly class CreateMessageAction
{
    public function __construct(private MessageRepository $repository) {}

    public function execute(MessageDTO $dto): Message
    {
        return $this->repository->create([
            'name' => $dto->name,
            'subject' => $dto->subject,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'message' => $dto->message,
            'read_at' => $dto->read_at,
        ]);
    }
}
