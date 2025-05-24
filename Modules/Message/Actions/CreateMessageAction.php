<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Repository\MessageRepository;

readonly class CreateMessageAction
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function execute(MessageDTO $dto): MessageDTO
    {
        $message = $this->repository->create([
            'content' => $dto->content,
        ]);

        return MessageDTO::fromArray($message->toArray());
    }
}
