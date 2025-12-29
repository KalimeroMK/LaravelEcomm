<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Attribute\Actions\CreateAttributeAction;
use Modules\Attribute\Actions\DeleteAttributeAction;
use Modules\Attribute\Actions\FindAttributeAction;
use Modules\Attribute\Actions\GetAllAttributesAction;
use Modules\Attribute\Actions\UpdateAttributeAction;
use Modules\Attribute\DTOs\AttributeDTO;
use Modules\Attribute\Http\Requests\Attribute\Store;
use Modules\Attribute\Http\Requests\Attribute\Update;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Repository\AttributeRepository;
use Modules\Attribute\Resource\AttributeResource;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;

class AttributeController extends CoreController
{
    public function __construct(
        public readonly AttributeRepository $repository,
        private readonly GetAllAttributesAction $getAllAttributesAction,
        private readonly FindAttributeAction $findAttributeAction,
        private readonly CreateAttributeAction $createAction,
        private readonly UpdateAttributeAction $updateAction,
        private readonly DeleteAttributeAction $deleteAction
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Attribute::class);

        $attributes = $this->getAllAttributesAction->execute();

        return AttributeResource::collection($attributes);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Attribute::class);
        $attribute = $this->createAction->execute(AttributeDTO::fromRequest($request));

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new AttributeResource($attribute));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $attribute = $this->findAttributeAction->execute($id);
        $this->authorize('view', $attribute);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new AttributeResource($attribute));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(AttributeRepository::class, 'update', $id);

        $existing = $this->repository->findById($id);
        $dto = AttributeDTO::fromRequest($request, $id, $existing instanceof Attribute ? $existing : null);

        $attribute = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new AttributeResource($attribute));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $attribute = $this->findAttributeAction->execute($id);
        $this->authorize('delete', $attribute);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }
}
