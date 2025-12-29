<?php

declare(strict_types=1);

namespace Modules\Post\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use Modules\Post\Actions\CreatePostAction;
use Modules\Post\Actions\DeletePostAction;
use Modules\Post\Actions\DeletePostMediaAction;
use Modules\Post\Actions\FindPostAction;
use Modules\Post\Actions\GetAllPostsAction;
use Modules\Post\Actions\ImportPostsAction;
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
        private readonly GetAllPostsAction $getAllAction,
        private readonly FindPostAction $findAction,
        private readonly CreatePostAction $createAction,
        private readonly UpdatePostAction $updateAction,
        private readonly DeletePostAction $deleteAction,
        private readonly DeletePostMediaAction $deletePostMediaAction,
        private readonly ImportPostsAction $importPostsAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Post::class);
        $posts = $this->getAllAction->execute();

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

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Post']))
            ->respond(null);
    }

    /**
     * Delete post media
     */
    public function deleteMedia(int $modelId, int $mediaId): JsonResponse
    {
        $this->authorizeFromRepo(PostRepository::class, 'update', $modelId);
        $this->deletePostMediaAction->execute($modelId, $mediaId);

        return $this
            ->setMessage('Media deleted successfully.')
            ->respond(null);
    }

    /**
     * Import posts from file
     */
    public function import(): JsonResponse
    {
        $this->authorize('create', Post::class);

        $file = request()->file('file');
        if (! $file) {
            return $this
                ->setMessage('Please upload a file.')
                ->setStatusCode(422)
                ->respond(null);
        }

        if (is_array($file)) {
            return $this
                ->setMessage('Please upload only one file.')
                ->setStatusCode(422)
                ->respond(null);
        }

        try {
            $this->importPostsAction->execute($file);

            return $this
                ->setMessage('Posts imported successfully.')
                ->respond(null);
        } catch (Exception $e) {
            return $this
                ->setMessage('An error occurred during import: '.$e->getMessage())
                ->setStatusCode(500)
                ->respond(null);
        }
    }
}
