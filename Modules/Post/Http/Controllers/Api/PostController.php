<?php

declare(strict_types=1);

namespace Modules\Post\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use Modules\Post\Actions\CreatePostAction;
use Modules\Post\Actions\DeletePostAction;
use Modules\Post\Actions\FindPostAction;
use Modules\Post\Actions\UpdatePostAction;
use Modules\Post\DTOs\PostDTO;
use Modules\Post\Http\Requests\Api\Store;
use Modules\Post\Http\Requests\Api\Update;
use Modules\Post\Http\Resources\PostResource;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;

class PostController extends CoreController
{
    public function __construct(
        private readonly PostRepository $repository,
        private readonly CreatePostAction $createAction,
        private readonly UpdatePostAction $updateAction,
        private readonly DeletePostAction $deleteAction,
        private readonly FindPostAction $findAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Post::class);
        $posts = $this->repository->findAll();

        return PostResource::collection($posts);
    }

    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $dto = PostDTO::fromRequest($request);
        $post = $this->createAction->execute($dto);
        SyncRelations::execute(
            $post,
            ['categories' => $dto->categories, 'tags' => $dto->tags]
        );
        MediaUploader::uploadMultiple($post, ['images'], 'post');

        return $this
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Post']))
            ->respond(new PostResource($post->fresh(['author', 'tags', 'categories'])));
    }

    public function show(int $id): JsonResponse
    {
        $this->authorizeFromRepo(PostRepository::class, 'view', $id);
        $post = $this->findAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', ['resource' => 'Post']))
            ->respond(new PostResource($post->fresh(['author', 'tags', 'categories'])));
    }

    public function update(Update $request, int $id): JsonResponse
    {
        $existingPost = $this->authorizeFromRepo(PostRepository::class, 'update', $id);
        $dto = PostDTO::fromRequest($request, $id, $existingPost);
        $post = $this->updateAction->execute($dto);
        SyncRelations::execute(
            $post,
            ['categories' => $dto->categories, 'tags' => $dto->tags]
        );
        /** @var Post $post */
        MediaUploader::clearAndUpload($post, ['images'], 'post');

        return $this
            ->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Post']))
            ->respond(new PostResource($post->fresh(['author', 'tags', 'categories'])));
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(PostRepository::class, 'delete', $id);

        $this->findAction->execute($id);
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Post']))
            ->respond(null);
    }
}
