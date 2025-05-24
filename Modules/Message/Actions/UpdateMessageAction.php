<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Repository\MessageRepository;

readonly class UpdateMessageAction
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function execute(int $id, array $data): MessageDTO
    {
        $message = $this->repository->update($id, $data);

        return MessageDTO::fromArray($message->toArray());
    }
}
