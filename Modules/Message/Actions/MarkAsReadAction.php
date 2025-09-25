<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Models\Message;

class MarkAsReadAction
{
    public function execute(Message $message): void
    {
        $message->update(['is_read' => true]);
    }
}
