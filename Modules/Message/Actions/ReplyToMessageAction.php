<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Models\Message;

class ReplyToMessageAction
{
    public function execute(Message $message, array $replyData): Message
    {
        return Message::create([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'subject' => 'Re: '.$message->subject,
            'message' => $replyData['message'],
            'parent_id' => $message->id,
            'is_read' => true,
        ]);
    }
}
