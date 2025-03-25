<?php

declare(strict_types=1);

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
        $this->middleware('permission:newsletter-list', ['only' => ['index']]);
        $this->middleware('permission:newsletter-show', ['only' => ['show']]);
        $this->middleware('permission:newsletter-create', ['only' => ['store']]);
        $this->middleware('permission:newsletter-edit', ['only' => ['update']]);
        $this->middleware('permission:newsletter-delete', ['only' => ['destroy']]);
    }

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
     * @throws ReflectionException
     */
    public function show(Newsletter $newsletter): JsonResponse
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
     * @throws ReflectionException
     */
    public function update(Update $request, Newsletter $newsletter): JsonResponse
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
     * @throws ReflectionException
     */
    public function destroy(Newsletter $newsletter): JsonResponse
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
