<?php

declare(strict_types=1);

namespace Modules\Message\DTOs;

class MessageListDTO
{
    public array $messages;

    public function __construct($messages)
    {
        $this->messages = $messages->toArray();
    }
}
