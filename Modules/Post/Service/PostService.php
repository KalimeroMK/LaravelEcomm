<?php

namespace Modules\Post\Service;

use Exception;
use Modules\Category\Models\Category;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;

class PostService extends CoreService
{
    use ImageUpload;
    
    private PostRepository $post_repository;
    
    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param $request
     *
     * @return null|string
     */
    public function store($request): null|string
    {
        try {
            $post = Post::create(
                $request->except('photo') + [
                    'photo' => $this->verifyAndStoreImage($request['photo']),
                ]
            );
            $post->categories()->attach($request['category']);
            $post->post_tag()->attach($request['tags']);
        } catch (Exception $exception) {
            return $exception;
        }
    }
    
    /**
     * @param $id
     *
     * @return array
     */
    public function edit($id): array
    {
        return [
            'categories' => Category::get(),
            'tags'       => Tag::get(),
            'users'      => User::get(),
            'post'       => $this->post_repository->findById($id),
        ];
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return array
     */
    public function create(): array
    {
        return [
            'categories' => Category::get(),
            'tags'       => Tag::get(),
            'users'      => User::get(),
            'post'       => new Post(),
        ];
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param $request
     * @param $post
     *
     * @return void
     */
    public function update($request, $post): void
    {
        if ($request->hasFile('photo')) {
            $post->update(
                $request->except('photo') + [
                    'photo' => $this->verifyAndStoreImage($request['photo']),
                ]
            );
        } else {
            $post->update($request->validated());
        }
        $post->post_tag()->sync($request['tags'], true);
        $post->categories()->sync($request['category'], true);
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
        $this->post_repository->delete($id);
    }
    
    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->post_repository->findAll();
    }
}