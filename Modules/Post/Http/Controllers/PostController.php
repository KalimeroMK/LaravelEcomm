<?php

namespace Modules\Post\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Post\Http\Requests\PostCategoryStore;
use Modules\Post\Http\Requests\Update;
use Modules\Post\Models\Post;
use Modules\Post\Service\PostService;

class PostController extends Controller
{
    private PostService $post_service;
    
    public function __construct(PostService $post_service)
    {
        $this->middleware('permission:post-list');
        $this->middleware('permission:post-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:post-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:post-delete', ['only' => ['destroy']]);
        $this->post_service = $post_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('post::index')->with($this->post_service->index());
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  PostCategoryStore  $request
     *
     * @return RedirectResponse
     */
    public function store(PostCategoryStore $request): RedirectResponse
    {
        $this->post_service->store($request);
        
        return redirect()->route('posts.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('post::create')->with($this->post_service->create());
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Post  $post
     *
     * @return Application|Factory|View
     */
    public function edit(Post $post)
    {
        return view('post::edit')->with($this->post_service->edit($post->id));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Post  $post
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Post $post): RedirectResponse
    {
        $this->post_service->update($request, $post);
        
        return redirect()->route('post.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Post  $post
     *
     * @return RedirectResponse
     */
    public function destroy(Post $post): RedirectResponse
    {
        $this->post_service->destroy($post->id);
        
        return redirect()->back();
    }
    
}
