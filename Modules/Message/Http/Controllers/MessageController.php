<?php

declare(strict_types=1);

namespace Modules\Message\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Message\Actions\DeleteMessageAction;
use Modules\Message\Actions\GetAllMessagesAction;
use Modules\Message\Models\Message;

class MessageController extends CoreController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $messagesDto = (new GetAllMessagesAction())->execute();

        return view('message::index', ['messages' => $messagesDto->messages]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message): Factory|View|Application
    {
        return view('message::show', ['message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message): RedirectResponse
    {
        (new DeleteMessageAction())->execute($message->id);

        return redirect()->back();
    }
}
