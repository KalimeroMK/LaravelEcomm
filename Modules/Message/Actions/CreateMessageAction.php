<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;

class CreateMessageAction
{
    public function execute(array $data): MessageDTO
    {
        $message = Message::create($data);

        return new MessageDTO($message);
    }
}
