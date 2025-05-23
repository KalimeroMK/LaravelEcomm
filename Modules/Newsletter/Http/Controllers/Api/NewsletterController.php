<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Newsletter\Actions\CreateNewsletterAction;
use Modules\Newsletter\Actions\DeleteNewsletterAction;
use Modules\Newsletter\Actions\GetAllNewslettersAction;
use Modules\Newsletter\Actions\GetNewsletterByIdAction;
use Modules\Newsletter\Actions\UpdateNewsletterAction;
use Modules\Newsletter\Http\Requests\Api\Store;
use Modules\Newsletter\Http\Resources\NewsletterResource;
use ReflectionException;

class NewsletterController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:newsletter-list', ['only' => ['index']]);
        $this->middleware('permission:newsletter-show', ['only' => ['show']]);
        $this->middleware('permission:newsletter-create', ['only' => ['store']]);
        $this->middleware('permission:newsletter-edit', ['only' => ['update']]);
        $this->middleware('permission:newsletter-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        $newslettersDto = (new GetAllNewslettersAction())->execute();

        return NewsletterResource::collection($newslettersDto->newsletters);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $newsletterDto = (new CreateNewsletterAction())->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Newsletter']))->respond(new NewsletterResource($newsletterDto));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $newsletterDto = (new GetNewsletterByIdAction())->execute($id);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Newsletter']))->respond(new NewsletterResource($newsletterDto));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Store $request, int $id): JsonResponse
    {
        $newsletterDto = (new UpdateNewsletterAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Newsletter']))->respond(new NewsletterResource($newsletterDto));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeleteNewsletterAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Newsletter']))->respond(null);
    }
}
