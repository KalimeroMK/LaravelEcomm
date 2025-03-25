<?php

declare(strict_types=1);

namespace Modules\Post\Service;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Modules\Core\Notifications\StatusNotification;
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
     * @throws Exception
     */
    public function store(Request $request): void
    {
        /** @var Post $post_info */
        $post_info = Post::getPostBySlug($request->slug);

        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'active';

        PostComment::create($data);

        $details = [
            'title' => 'New Comment created',
            'actionURL' => route('front.blog-detail', $post_info->slug),
            'fas' => 'fas fa-comment',
        ];

        Notification::send(User::role('super-admin')->get(), new StatusNotification($details));
    }

    /**
     * Edit the specified resource.
     */
    public function edit(int $id): Model
    {
        return $this->post_comment_repository->findById($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(array $data, int $id): Model
    {
        return $this->post_comment_repository->update($id, $data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id): void
    {
        $this->post_comment_repository->delete($id);
    }

    /**
     * Get all resources.
     */
    public function index(): Collection
    {
        return $this->post_comment_repository->findAll();
    }
}
