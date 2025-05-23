<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Models\Message;

class ShowMessageAction
{
    public function execute(int $id): MessageDTO
    {
        $message = Message::findOrFail($id);

        return new MessageDTO($message);
    }
}
