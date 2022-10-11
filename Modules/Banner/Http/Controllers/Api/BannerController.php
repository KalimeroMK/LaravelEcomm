<?php

namespace Modules\Banner\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Modules\Attribute\Http\Requests\Api\SearchRequest;
use Modules\Banner\Exception\SearchException;
use Modules\Banner\Http\Requests\Api\Store;
use Modules\Banner\Http\Requests\Api\Update;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Banner\Service\BannerService;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;

class BannerController extends CoreController
{
    
    private BannerService $banner_service;
    
    public function __construct(BannerService $banner_service)
    {
        $this->banner_service = $banner_service;
    }
    
    /**
     * @throws SearchException
     */
    public function index(SearchRequest $request)
    {
        try {
            return BannerResource::collection($this->banner_service->search($request->validated()));
        } catch (Exception $exception) {
            throw new SearchException($exception);
        }
    }
    
    /**
     * @param  Store  $request
     *
     * @return mixed
     * @throws Exception
     */
    public function store(Store $request)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->banner_service->banner_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new BannerResource($this->banner_service->store($request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function show($id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.ok',
                        [
                            'resource' => Helper::getResourceName(
                                $this->banner_service->banner_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new BannerResource($this->banner_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return string
     */
    public function update(Update $request, $id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.updateSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->banner_service->banner_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new BannerResource($this->banner_service->update($id, $request->validated())));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $id
     *
     * @return JsonResponse|string
     */
    public function destroy($id)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.deleteSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->banner_service->banner_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->banner_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
