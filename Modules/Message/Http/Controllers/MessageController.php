<?php

declare(strict_types=1);

namespace Modules\Message\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Message\Actions\DeleteMessageAction;
use Modules\Message\Actions\GetAllMessagesAction;
use Modules\Message\Actions\MarkAsReadAction;
use Modules\Message\Actions\MarkMultipleAsReadAction;
use Modules\Message\Actions\ReplyToMessageAction;
use Modules\Message\Actions\ShowMessageAction;
use Modules\Message\Models\Message;

class MessageController extends CoreController
{
    private GetAllMessagesAction $getAllAction;

    private DeleteMessageAction $deleteAction;

    private MarkAsReadAction $markAsReadAction;

    private ReplyToMessageAction $replyAction;

    private MarkMultipleAsReadAction $markMultipleAsReadAction;

    private ShowMessageAction $showAction;

    public function __construct(
        GetAllMessagesAction $getAllAction,
        DeleteMessageAction $deleteAction,
        MarkAsReadAction $markAsReadAction,
        ReplyToMessageAction $replyAction,
        MarkMultipleAsReadAction $markMultipleAsReadAction,
        ShowMessageAction $showAction,
    ) {
        $this->getAllAction = $getAllAction;
        $this->deleteAction = $deleteAction;
        $this->markAsReadAction = $markAsReadAction;
        $this->replyAction = $replyAction;
        $this->markMultipleAsReadAction = $markMultipleAsReadAction;
        $this->showAction = $showAction;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View|Factory|Application
    {
        $this->authorize('viewAny', Message::class);
        $messages = $this->getAllAction->execute();

        return view('message::index', ['messages' => $messages]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message): Factory|View|Application
    {
        $this->authorize('view', $message);
        $message = $this->showAction->execute($message->id);

        return view('message::show', ['message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message): RedirectResponse
    {
        $this->authorize('delete', $message);
        $this->deleteAction->execute($message->id);

        return redirect()->back();
    }

    /**
     * Mark message as read
     */
    public function markAsRead(Message $message): RedirectResponse
    {
        $this->authorize('update', $message);
        $this->markAsReadAction->execute($message);

        return redirect()->back();
    }

    /**
     * Reply to message
     */
    public function reply(Message $message, Request $request): RedirectResponse
    {
        $this->authorize('update', $message);
        $replyData = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $this->replyAction->execute($message, $replyData);

        return redirect()->back()->with('success', 'Reply sent successfully!');
    }

    /**
     * Mark multiple messages as read
     */
    public function markMultipleAsRead(Request $request): RedirectResponse
    {
        $this->authorize('viewAny', Message::class);
        $messageIds = $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id',
        ])['message_ids'];

        $this->markMultipleAsReadAction->execute($messageIds);

        return redirect()->back()->with('success', 'Messages marked as read!');
    }
}
