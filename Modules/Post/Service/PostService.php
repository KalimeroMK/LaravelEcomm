<?php

namespace Modules\Post\Service;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Service\CoreService;
use Modules\Post\Repository\PostRepository;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;
use Spatie\MediaLibrary\MediaCollections\FileAdder;

class PostService extends CoreService
{
    public PostRepository $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        parent::__construct($post_repository);
        $this->post_repository = $post_repository;
    }

    /**
     * Create a new post with possible media files.
     *
     * @param  array<string, mixed>  $data  The data for creating the post.
     * @return Model The newly created post model.
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function create(array $data): Model
    {
        $post = $this->post_repository->create($data);
        if (isset($data['category'])) {
            $post->categories()->attach($data['category']);
        }

        if (isset($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }

        // Handle image uploads
        if (request()->hasFile('images')) {
            $post->addMultipleMediaFromRequest(['images'])
                ->each(function (FileAdder $fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('post');
                });
        }

        return $post;
    }

    /**
     * Update an existing post with new data and possibly new media files.
     *
     * @param  int  $id  The post ID to update.
     * @param  array<string, mixed>  $data  The data for updating the post.
     * @return Model The updated post model.
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(int $id, array $data): Model
    {
        $post = $this->post_repository->findById($id);

        $post->update($data);

        if (isset($data['category'])) {
            $post->categories()->sync($data['category']);
        }

        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }

        // Check for new image uploads and handle them
        if (request()->hasFile('images')) {
            $post->clearMediaCollection('post'); // Optionally clear existing media
            $post->addMultipleMediaFromRequest(['images'])
                ->each(function (FileAdder $fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('post');
                });
        }

        return $post;
    }

    /**
     * Search posts based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): LengthAwarePaginator
    {
        return $this->post_repository->search($data);
    }
}
