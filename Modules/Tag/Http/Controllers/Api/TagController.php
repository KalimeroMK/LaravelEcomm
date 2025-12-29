<?php

declare(strict_types=1);

namespace Modules\Tag\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Tag\Actions\CreateTagAction;
use Modules\Tag\Actions\DeleteTagAction;
use Modules\Tag\Actions\GetAllTagsAction;
use Modules\Tag\Actions\ShowTagAction;
use Modules\Tag\Actions\UpdateTagAction;
use Modules\Tag\DTOs\TagDto;
use Modules\Tag\Http\Requests\Api\Store;
use Modules\Tag\Http\Requests\Api\Update;
use Modules\Tag\Http\Resources\TagResource;
use Modules\Tag\Models\Tag;
use Modules\Tag\Repository\TagRepository;
use ReflectionException;

class TagController extends CoreController
{
    private readonly CreateTagAction $createTagAction;

    private readonly UpdateTagAction $updateTagAction;

    private readonly DeleteTagAction $deleteTagAction;

    private readonly GetAllTagsAction $getAllTagsAction;

    private readonly ShowTagAction $showTagAction;

    public function __construct(
        CreateTagAction $createTagAction,
        UpdateTagAction $updateTagAction,
        DeleteTagAction $deleteTagAction,
        GetAllTagsAction $getAllTagsAction,
        ShowTagAction $showTagAction
    ) {
        $this->createTagAction = $createTagAction;
        $this->updateTagAction = $updateTagAction;
        $this->deleteTagAction = $deleteTagAction;
        $this->getAllTagsAction = $getAllTagsAction;
        $this->showTagAction = $showTagAction;
    }

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Tag::class);

        return TagResource::collection($this->getAllTagsAction->execute());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Tag::class);
        $dto = TagDto::fromRequest($request);
        $tag = $this->createTagAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName($tag),
                    ]
                )
            )
            ->respond(new TagResource($tag));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeFromRepo(TagRepository::class, 'view', $id);
        $tag = $this->showTagAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName($tag),
                    ]
                )
            )
            ->respond(new TagResource($tag));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(TagRepository::class, 'update', $id);
        $dto = TagDto::fromRequest($request, $id);
        $updatedTag = $this->updateTagAction->execute($dto);

        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName($updatedTag),
                    ]
                )
            )
            ->respond(new TagResource($updatedTag));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(TagRepository::class, 'delete', $id);
        $this->deleteTagAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => 'Tag',
                    ]
                )
            )
            ->respond(null);
    }
}
