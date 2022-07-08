<?php

namespace Modules\Message\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Message\Models\Message;
use Modules\Message\Service\MessageService;

class MessageController extends Controller
{
    private MessageService $message_service;
    
    public function __construct() { $this->message_service = new MessageService(); }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return $this->message_service->index();
    }
    
    /**
     * Display the specified resource.
     *
     * @param  Message  $message
     *
     * @return Application|Factory|View
     */
    public function show(Message $message): Factory|View|Application
    {
        return $this->message_service->show($message);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Message  $message
     *
     * @return RedirectResponse
     */
    public function destroy(Message $message): RedirectResponse
    {
        return $this->message_service->destroy($message);
    }
}
