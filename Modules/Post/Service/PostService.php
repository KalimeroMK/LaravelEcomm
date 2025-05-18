<?php

declare(strict_types=1);

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
     * Create a new post with categories and media.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function createWithCategoriesAndMedia(array $data): Model
    {
        $post = $this->post_repository->create($data);
        if (isset($data['category'])) {
            $post->categories()->attach($data['category']);
        }

        if (isset($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }

        // Handle image uploads
        if (isset($data['images'])) {
            $post->clearMediaCollection('post');
            $post->addMultipleMediaFromRequest(['images'])
                ->each(function (FileAdder $fileAdder): void {
                    $fileAdder->preservingOriginal()->toMediaCollection('post');
                });
        }

        return $post;
    }

    /**
     * Update an existing post with categories, tags, and media.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function updateWithCategoriesAndMedia(int $id, array $data): Model
    {
        $post = $this->post_repository->findById($id);
        $post->update($data);
        if (isset($data['category'])) {
            $post->categories()->sync($data['category']);
        }
        if (isset($data['tags'])) {
            $post->tags()->sync($data['tags']);
        }
        if (isset($data['images'])) {
            $post->clearMediaCollection('post');
            $post->addMultipleMediaFromRequest(['images'])
                ->each(function (FileAdder $fileAdder): void {
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
