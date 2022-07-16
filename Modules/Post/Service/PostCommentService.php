<?php

namespace Modules\Post\Service;

use App\Notifications\StatusNotification;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostComment;
use Modules\Post\Repository\PostCommentRepository;
use Modules\User\Models\User;

class PostCommentService
{
    private PostCommentRepository $post_comment_repository;
    
    public function __construct(PostCommentRepository $post_comment_repository)
    {
        $this->post_comment_repository = $post_comment_repository;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     *
     * @return void
     */
    public function store($request): void
    {
        $post_info       = Post::getPostBySlug($request->slug);
        $data            = $request;
        $data['user_id'] = $request->user()->id;
        $data['status']  = 'active';
        PostComment::create($data);
        $details = [
            'title'     => "New Comment created",
            'actionURL' => route('blog.detail', $post_info->slug),
            'fas'       => 'fas fa-comment',
        ];
        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
    }
    
    /**
     * @param $id
     *
     * @return mixed
     */
    public function edit($id): mixed
    {
        return $this->post_comment_repository->findById($id);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $data
     * @param $id
     *
     * @return RedirectResponse
     */
    public function update($data, $id): RedirectResponse
    {
        return $this->post_comment_repository->update($id, $data);
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->post_comment_repository->delete($id);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): View|Factory|Application
    {
        return $this->post_comment_repository->findAll();
    }
}