<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use App\Events\MessageSent;
use Exception;
use Illuminate\Support\Facades\Auth;
use Modules\Message\Models\Message;

class MessageStoreAction
{
    /**
     * @throws Exception
     */
    public function execute(array $data): Message
    {
        $message = Message::create($data);

        $eventData = [];
        $eventData['url'] = route('message.show', $message->id);
        $eventData['date'] = $message->created_at->format('F d, Y h:i A');
        $eventData['name'] = $message->name;
        $eventData['email'] = $message->email;
        $eventData['phone'] = $message->phone;
        $eventData['message'] = $message->message;
        $eventData['subject'] = $message->subject;
        $eventData['photo'] = Auth::user()->photo ?? '';

        event(new MessageSent($eventData));

        return $message;
    }
}
