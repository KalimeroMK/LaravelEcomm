<?php

namespace Modules\Message\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Message\Models\Message;
use Modules\Message\Service\MessageService;

class MessageController extends CoreController
{
    private MessageService $message_service;

    public function __construct(MessageService $message_service)
    {
        $this->message_service = $message_service;
        $this->authorizeResource(Message::class, 'message');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('message::index', ['messages' => $this->message_service->getAll()]);
    }

    /**
     * Display the specified resource.
     *
     * @param Message $message
     *
     * @return Application|Factory|View
     */
    public function show(Message $message): Factory|View|Application
    {
        return view('message::show', ['message' => $this->message_service->show($message->id)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Message $message
     *
     * @return RedirectResponse
     */
    public function destroy(Message $message): RedirectResponse
    {
        $this->message_service->delete($message->id);

        return redirect()->back();
    }
}
