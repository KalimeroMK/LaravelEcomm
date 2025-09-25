<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Models\Message;

class ReplyToMessageAction
{
    public function execute(Message $message, array $replyData): Message
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized - Admin access required');
        }
        
        return Message::create([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'subject' => 'Re: ' . $message->subject,
            'message' => $replyData['message'],
            'parent_id' => $message->id,
            'is_read' => true,
        ]);
    }
}
