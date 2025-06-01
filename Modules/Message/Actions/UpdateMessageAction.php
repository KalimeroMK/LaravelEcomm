<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Repository\MessageRepository;

readonly class UpdateMessageAction
{
    public function __construct(private MessageRepository $repository) {}

    public function execute(MessageDTO $dto): Model
    {
        $message = $this->repository->findById($dto->id);

        $message->update([
            'name' => $dto->name ?? $message->name,
            'subject' => $dto->subject ?? $message->subject,
            'email' => $dto->email ?? $message->email,
            'phone' => $dto->phone ?? $message->phone,
            'message' => $dto->message ?? $message->message,
        ]);

        return $message;
    }
}
