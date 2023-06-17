<?php

namespace Modules\Post\Service;

use Exception;
use Illuminate\Http\Request;
use Modules\Category\Models\Category;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;
use Modules\Product\Exceptions\SearchException;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;

class PostService extends CoreService
{
    use ImageUpload;

    public PostRepository $post_repository;

    public function __construct(PostRepository $post_repository)
    {
        $this->post_repository = $post_repository;
    }

    /**
     * @param $data
     *
     * @return Exception|void
     */
    public function store($data)
    {
            $post = $this->post_repository->create(
                collect($data)->except(['photo'])->toArray() + [
                    'photo' => $this->verifyAndStoreImage($data['photo']),
                ]
            );
            $post->categories()->attach($data['category']);
            $post->post_tag()->attach($data['tags']);
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
     * @param $data
     * @param $post
     *
     * @return Exception|void
     */
    public function update($data, $post)
    {
            if ($data->hasFile('photo')) {
                $post->update(
                    $data->except('photo') + [
                        'photo' => $this->verifyAndStoreImage($data['photo']),
                    ]
                );
            } else {
                $post->update($data);
            }
            $post->post_tag()->sync($data['tags'], true);
            $post->categories()->sync($data['category'], true);
    }

    /**
     * @param $id
     *
     * @return Exception|void
     */
    public function destroy($id)
    {
            $this->post_repository->delete($id);
    }

    /**
     * @param $data
     *
     * @return mixed
     * @throws SearchException
     */
    public function getAll($data): mixed
    {
            return $this->post_repository->search($data);
    }

    /**
     * @param  Request  $request
     *
     * @return void
     */
    public function upload(Request $request): void
    {
        if ($request->hasFile('upload')) {
            $originName = $request->file('upload')->getClientOriginalName();
            $fileName   = pathinfo($originName, PATHINFO_FILENAME);
            $extension  = $request->file('upload')->getClientOriginalExtension();
            $fileName   = $fileName . '_' . time() . '.' . $extension;

            $request->file('upload')->move(public_path('images'), $fileName);

            $CKEditorFuncNum = $request->input('CKEditorFuncNum');
            $url             = asset('images/' . $fileName);
            $msg             = 'Image uploaded successfully';
            $response        = "<script>window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg')</script>";

            @header('Content-type: text/html; charset=utf-8');
            echo $response;
        }
    }

    /**
     * @param $id
     *
     * @return mixed|string
     */
    public function show($id): mixed
    {
        try {
            return $this->post_repository->findById($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
