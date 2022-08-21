<?php

namespace Modules\Newsletter\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Core\Helpers\Helper;
use Modules\Core\Traits\ApiResponses;
use Modules\Coupon\Http\Requests\Api\Store;
use Modules\Coupon\Http\Requests\Api\Update;
use Modules\Coupon\Http\Resource\CouponResource;
use Modules\Newsletter\Http\Resources\NewsletterResource;
use Modules\Newsletter\Service\NewsletterService;

class NewsletterController extends Controller
{
    use ApiResponses;
    
    private NewsletterService $newsletter_service;
    
    public function __construct(NewsletterService $newsletter_service)
    {
        $this->newsletter_service = $newsletter_service;
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return NewsletterResource::collection($this->newsletter_service->getAll());
    }
    
    public function store(Store $request)
    {
        try {
            return $this
                ->setMessage(
                    __(
                        'apiResponse.storeSuccess',
                        [
                            'resource' => Helper::getResourceName(
                                $this->newsletter_service->newsletter_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new BannerResource($this->newsletter_service->store($request->validated())));
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
                                $this->newsletter_service->newsletter_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new CouponResource($this->newsletter_service->show($id)));
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
                                $this->newsletter_service->newsletter_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new BannerResource($this->newsletter_service->update($id, $request->validated())));
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
                                $this->newsletter_service->newsletter_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->newsletter_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
