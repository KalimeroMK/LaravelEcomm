<?php

declare(strict_types=1);

namespace Modules\Banner\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Actions\CreateBannerAction;
use Modules\Banner\Actions\DeleteBannerAction;
use Modules\Banner\Actions\UpdateBannerAction;
use Modules\Banner\DTO\BannerDTO;
use Modules\Banner\Http\Requests\Api\Store;
use Modules\Banner\Http\Requests\Api\Update;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BannerController extends CoreController
{
    private BannerRepository $banner_repository;

    private CreateBannerAction $createBannerAction;

    private UpdateBannerAction $updateBannerAction;

    private DeleteBannerAction $deleteBannerAction;

    public function __construct(
        BannerRepository $banner_repository,
        CreateBannerAction $createBannerAction,
        UpdateBannerAction $updateBannerAction,
        DeleteBannerAction $deleteBannerAction
    ) {
        $this->banner_repository = $banner_repository;
        $this->createBannerAction = $createBannerAction;
        $this->updateBannerAction = $updateBannerAction;
        $this->deleteBannerAction = $deleteBannerAction;
        $this->middleware('permission:banner-list', ['only' => ['index']]);
        $this->middleware('permission:banner-show', ['only' => ['show']]);
        $this->middleware('permission:banner-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banner-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return BannerResource::collection($this->banner_repository->all());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource(
                $this->createBannerAction->execute(BannerDTO::fromRequest($request))
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(
                            $this->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->banner_repository->findById($id)));
    }

    /**
     * @throws ReflectionException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $dto = BannerDTO::fromRequest($request)->withId($id);

        $banner = $this->updateBannerAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->banner_repository->model),
            ]))
            ->respond(new BannerResource($banner));
    }


    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteBannerAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->banner_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
