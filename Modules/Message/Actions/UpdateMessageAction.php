<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;

class UpdateMessageAction
{
    public function execute(int $id, array $data): MessageDTO
    {
        $message = Message::findOrFail($id);
        $message->update($data);

        return new MessageDTO($message);
    }
}
