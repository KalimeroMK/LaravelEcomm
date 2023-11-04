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
    private PostCommentService $postCommentService;

    public function __construct(PostCommentService $postCommentService)
    {
        $this->postCommentService = $postCommentService;
        $this->authorizeResource(PostComment::class, 'comment');
    }


    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('post::comment.index', ['comments' => $this->postCommentService->index()]);
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
        $this->postCommentService->store($request);

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  PostComment  $comment
     *
     * @return Application|Factory|View
     */
    public function edit(PostComment $comment)
    {
        return view('post::comment.edit', ['comment' => $this->postCommentService->edit($comment->id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  PostComment  $comment
     *
     * @return Application|Factory|View
     */
    public function update(Request $request, PostComment $comment): Application|Factory|View
    {
        return view('post::comment.index')->with($this->postCommentService->update($request, $comment->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  PostComment  $comment
     *
     * @return RedirectResponse
     */
    public function destroy(PostComment $comment): RedirectResponse
    {
        $this->postCommentService->destroy($comment->id);

        return redirect()->back();
    }
}
