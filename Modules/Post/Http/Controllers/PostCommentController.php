<?php

namespace Modules\Post\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Post\Models\PostComment;
use Modules\Post\Service\PostCommentService;

class PostCommentController extends Controller
{
    private PostCommentService $post_comment_service;
    
    public function __construct(PostCommentService $post_comment_service)
    {
        $this->post_comment_service = $post_comment_service;
        $this->middleware('permission:comments-list');
        $this->middleware('permission:comments-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:comments-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:comments-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('post::comment.index', ['comments' => $this->post_comment_service->index()]);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->post_comment_service->store($request);
        
        return redirect()->back();
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  PostComment  $postComment
     *
     * @return Application|Factory|View
     */
    public function edit(PostComment $postComment)
    {
        return view('post::comment.edit', ['comment' => $this->post_comment_service->edit($postComment->id)]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  PostComment  $postComment
     *
     * @return Application|Factory|View
     */
    public function update(Request $request, PostComment $postComment): Application|Factory|View
    {
        return view('post::comment.index')->with($this->post_comment_service->update($request, $postComment->id));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  PostComment  $postComment
     *
     * @return RedirectResponse
     */
    public function destroy(PostComment $postComment): RedirectResponse
    {
        $this->post_comment_service->destroy($postComment->id);
        
        return redirect()->back();
    }
}
