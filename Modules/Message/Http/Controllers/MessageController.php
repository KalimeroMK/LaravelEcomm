<?php

namespace Modules\Message\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Admin\Models\Message;
use Modules\Message\Service\MessageService;

class MessageController extends Controller
{
    private MessageService $message_service;
    
    public function __construct(MessageService $message_service)
    {
        $this->message_service = $message_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return view('backend::message.index')->with($this->message_service->index());
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
        return view('backend::message.show')->with($this->message_service->show($message->id));
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
        $this->message_service->destroy($message->id);
        
        return redirect()->back();
    }
}
