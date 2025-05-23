<?php

declare(strict_types=1);

namespace Modules\Front\Actions;

use App\Events\MessageSent;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Message\Models\Message;

class MessageStoreAction
{
    public function __invoke(Request $request): string
    {
        try {
            $message = Message::create($request->all());
            $data = [];
            $data['url'] = route('message.show', $message->id);
            $data['date'] = $message->created_at->format('F d, Y h:i A');
            $data['name'] = $message->name;
            $data['email'] = $message->email;
            $data['phone'] = $message->phone;
            $data['message'] = $message->message;
            $data['subject'] = $message->subject;
            $data['photo'] = Auth::user()->photo ?? '';
            event(new MessageSent($data));

            return 'message sent';
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
