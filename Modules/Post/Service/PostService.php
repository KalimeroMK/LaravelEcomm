<?php

namespace Modules\Post\Service;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Modules\Core\Service\CoreService;
use Modules\Post\Repository\PostRepository;

class PostService extends CoreService
{

    public PostRepository $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        parent::__construct($post_repository);
        $this->post_repository = $post_repository;
    }


    /**
     * Create a new banner with possible media files.
     *
     * @param array<string, mixed> $data The data for creating the banner.
     * @return Model The newly created banner model.
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
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('post');
                });
        }

        return $post;
    }

    /**
     * Update an existing banner with new data and possibly new media files.
     *
     * @param int $id The banner ID to update.
     * @param array<string, mixed> $data The data for updating the banner.
     * @return Model The updated banner model.
     */
    public function update(int $id, array $data): Model
    {
        $post = $this->post_repository->findById($id);

        $post->update($data);

        if (isset($data['category'])) {
            $post->categories()->attach($data['category']);
        }

        if (isset($data['tags'])) {
            $post->tags()->attach($data['tags']);
        }
        // Check for new image uploads and handle them
        if (request()->hasFile('images')) {
            $post->clearMediaCollection('post'); // Optionally clear existing media
            $post->addMultipleMediaFromRequest(['images'])
                ->each(function ($fileAdder) {
                    $fileAdder->preservingOriginal()->toMediaCollection('post');
                });
        }

        return $post;
    }


    /**
     * Search posts based on given data.
     *
     * @param array<string, mixed> $data
     * @return LengthAwarePaginator
     */
    public function search(array $data): LengthAwarePaginator
    {
        return $this->post_repository->search($data);
    }

    /**
     * Upload an image.
     *
     * @param Request $request
     * @return void
     */
    public function upload(Request $request): void
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName = pathinfo($originName, PATHINFO_FILENAME);
            $extension = $request->file('upload')->getClientOriginalExtension();
            $fileName = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->move(public_path('images'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url = asset('images/' . $fileName);
            $msg = 'Image uploaded successfully';
            $response = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }
}
