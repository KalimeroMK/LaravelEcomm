<?php

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Banner\Http\Resource\BannerResource;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Coupon\Http\Resource\CouponResource;
use Modules\Newsletter\Http\Requests\Api\Store;
use Modules\Newsletter\Http\Requests\Api\Store as Update;
use Modules\Newsletter\Http\Resources\NewsletterResource;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Service\NewsletterService;
use ReflectionException;

class NewsletterController extends CoreController
{

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

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
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
            ->respond(new NewsletterResource($this->newsletter_service->create($request->validated())));
    }


    /**
     * @param Newsletter $newsletter
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function show(Newsletter $newsletter)
    {
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
            ->respond(new CouponResource($this->newsletter_service->findById($newsletter->id)));
    }

    /**
     * @param Update $request
     * @param Newsletter $newsletter
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Update $request, Newsletter $newsletter)
    {
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
            ->respond(new BannerResource($this->newsletter_service->update($newsletter->id, $request->validated())));
    }

    /**
     * @param Newsletter $newsletter
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function destroy(Newsletter $newsletter)
    {
        $this->newsletter_service->delete($newsletter->id);
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
            ->respond(null);
    }
}
