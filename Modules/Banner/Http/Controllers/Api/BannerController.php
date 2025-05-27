<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Actions\CreateBannerAction;
use Modules\Banner\Actions\DeleteBannerAction;
use Modules\Banner\Actions\UpdateBannerAction;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Http\Requests\Api\Store;
use Modules\Banner\Http\Requests\Api\Update;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BannerController extends CoreController
{
    public function __construct(
        private readonly BannerRepository $repository,
        private readonly CreateBannerAction $createAction,
        private readonly UpdateBannerAction $updateAction,
        private readonly DeleteBannerAction $deleteAction
    ) {
        // Removed permission middleware, switched to policies
    }

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Banner::class);

        return BannerResource::collection($this->repository->all());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Banner::class);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BannerResource(
                $this->createAction->execute(BannerDTO::fromRequest($request))
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $banner = $this->authorizeFromRepo(BannerRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BannerResource($banner));
    }

    /**
     * @throws ReflectionException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(BannerRepository::class, 'update', $id);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BannerResource(
                $this->updateAction->execute(BannerDTO::fromArray($request->validated())->withId($id))
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(BannerRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }
}
