<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Actions\CreateBannerAction;
use Modules\Banner\Actions\DeleteBannerAction;
use Modules\Banner\Actions\FindBannerAction;
use Modules\Banner\Actions\GetAllBannersAction;
use Modules\Banner\Actions\UpdateBannerAction;
use Modules\Banner\DTOs\BannerDTO;
use Modules\Banner\Http\Requests\Api\Store;
use Modules\Banner\Http\Requests\Api\Update;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Banner\Models\Banner;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use ReflectionException;

class BannerController extends CoreController
{
    public function __construct(
        private readonly BannerRepository $repository,
        private readonly GetAllBannersAction $getAllBannersAction,
        private readonly FindBannerAction $findBannerAction,
        private readonly CreateBannerAction $createAction,
        private readonly UpdateBannerAction $updateAction,
        private readonly DeleteBannerAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Banner::class);

        $banners = $this->getAllBannersAction->execute();

        return BannerResource::collection($banners);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Banner::class);

        $dto = BannerDTO::fromRequest($request);
        $banner = $this->createAction->execute($dto);

        MediaUploader::uploadMultiple($banner, ['images'], 'banner');

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BannerResource($banner->fresh('media')));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $banner = $this->findBannerAction->execute($id);
        $this->authorize('view', $banner);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BannerResource($banner));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(BannerRepository::class, 'update', $id);

        $dto = BannerDTO::fromRequest($request, $id, $this->repository->findById($id));
        $banner = $this->updateAction->execute($dto);

        /** @var Banner $banner */
        MediaUploader::clearAndUpload($banner, ['images'], 'banner');

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new BannerResource($banner->fresh('media')));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $banner = $this->findBannerAction->execute($id);
        $this->authorize('delete', $banner);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }
}
