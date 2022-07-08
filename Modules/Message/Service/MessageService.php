<?php

namespace Modules\Message\Service;

use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;

class MessageService
{
    private MessageRepository $message_repository;
    
    public function __construct(MessageRepository $message_repository)
    {
        $this->message_repository = $message_repository;
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
        if (true) {
            $message->read_at = Carbon::now();
            $message->save();
            
            return view('backend::message.show', compact('message'));
        }
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
        $status = $message->delete();
        if ($status) {
            request()->session()->flash('success', 'Successfully deleted message');
        } else {
            request()->session()->flash('error', 'Error occurred please try again');
        }
        
        return back();
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        $messages = $this->message_repository->getAll();
        
        return view('backend::message.index', compact('messages'));
    }
}