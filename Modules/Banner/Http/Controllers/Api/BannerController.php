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
use Modules\Banner\Repository\BannerRepository;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use ReflectionException;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BannerController extends CoreController
{
    private BannerRepository $repository;

    private CreateBannerAction $createAction;

    private UpdateBannerAction $updateAction;

    private DeleteBannerAction $deleteAction;

    public function __construct(
        BannerRepository $repository,
        CreateBannerAction $createBannerAction,
        UpdateBannerAction $updateBannerAction,
        DeleteBannerAction $deleteBannerAction
    ) {
        $this->repository = $repository;
        $this->createAction = $createBannerAction;
        $this->updateAction = $updateBannerAction;
        $this->deleteAction = $deleteBannerAction;
        $this->middleware('permission:banner-list', ['only' => ['index']]);
        $this->middleware('permission:banner-show', ['only' => ['show']]);
        $this->middleware('permission:banner-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:banner-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return BannerResource::collection($this->repository->all());
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
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource(
                $this->createAction->execute(BannerDTO::fromRequest($request))
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
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource($this->repository->findById($id)));
    }

    /**
     * @throws ReflectionException
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Update $request, int $id): JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.storeSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(new BannerResource(
                $this->updateAction->execute(BannerDTO::fromRequest($request))
            ));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->deleteAction->execute($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
