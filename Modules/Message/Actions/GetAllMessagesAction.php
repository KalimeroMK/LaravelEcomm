<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageListDTO;
use Modules\Message\Models\Message;

class GetAllMessagesAction
{
    public function execute(): MessageListDTO
    {
        $messages = Message::all();

        return new MessageListDTO($messages);
    }
}
