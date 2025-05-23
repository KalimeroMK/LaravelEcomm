<?php

declare(strict_types=1);

namespace Modules\Message\DTOs;

use Modules\Message\Models\Message;

class MessageDTO
{
    public int $id;

    public string $content;

    public string $created_at;

    public function __construct(Message $message)
    {
        $this->id = $message->id;
        $this->content = $message->content;
        $this->created_at = $message->created_at->toDateTimeString();
    }
}
