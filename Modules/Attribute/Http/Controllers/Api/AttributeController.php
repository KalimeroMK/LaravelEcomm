<?php

namespace Modules\Attribute\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Modules\Attribute\Exceptions\SearchException;
use Modules\Attribute\Http\Requests\Api\SearchRequest;
use Modules\Attribute\Resource\AttributeResource;
use Modules\Attribute\Service\AttributeService;
use Modules\Brand\Http\Requests\Api\Update;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;
use Modules\Coupon\Http\Requests\Api\Store;

class AttributeController extends Controller
{
    use ApiResponses;
    
    public AttributeService $attribute_service;
    
    public function __construct(AttributeService $attribute_service)
    {
        $this->attribute_service = $attribute_service;
    }
    
    /**
     * @param  SearchRequest  $request
     *
     * @return AnonymousResourceCollection
     * @throws SearchException
     */
    public function index(SearchRequest $request)
    {
        try {
            return AttributeResource::collection($this->attribute_service->search($request->validated()));
        } catch (Exception $exception) {
            throw new SearchException($exception);
        }
    }
    
    /**
     * @param  Store  $request
     *
     * @return JsonResponse|string
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
                                $this->attribute_service->attribute_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new AttributeResource($this->attribute_service->store($request->validated())));
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
                                $this->attribute_service->attribute_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new AttributeResource($this->attribute_service->show($id)));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param  Update  $request
     * @param $id
     *
     * @return JsonResponse|string
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
                                $this->attribute_service->attribute_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new AttributeResource($this->attribute_service->update($id, $request->all())));
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
                                $this->attribute_service->attribute_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->attribute_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
