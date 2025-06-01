<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Http\Resource\BundleResource;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Repository\BundleRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use ReflectionException;

class BundleController extends CoreController
{
    public function __construct(
        private readonly BundleRepository $repository,
        private readonly CreateBundleAction $createAction,
        private readonly UpdateBundleAction $updateAction,
        private readonly DeleteBundleAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Bundle::class);

        return BundleResource::collection($this->repository->findAll());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Bundle::class);

        $dto = BundleDTO::fromRequest($request);
        $bundle = $this->createAction->execute($dto);

        SyncRelations::execute($bundle, ['products' => $dto->products]);
        MediaUploader::uploadMultiple($bundle, ['images'], 'bundle');

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BundleResource($bundle->fresh(['media', 'products'])));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $bundle = $this->authorizeFromRepo(BundleRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BundleResource($bundle));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(BundleRepository::class, 'update', $id);

        $dto = BundleDTO::fromRequest($request, $id, $this->repository->findById($id));
        $bundle = $this->updateAction->execute($dto);

        SyncRelations::execute($bundle, ['products' => $dto->products]);
        /** @var Bundle $bundle */
        MediaUploader::clearAndUpload($bundle, ['images'], 'bundle');

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BundleResource($bundle->fresh(['media', 'products'])));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(BundleRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }
}
