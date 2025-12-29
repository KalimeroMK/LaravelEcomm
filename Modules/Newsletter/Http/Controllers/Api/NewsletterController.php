<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Newsletter\Actions\CreateNewsletterAction;
use Modules\Newsletter\Actions\DeleteNewsletterAction;
use Modules\Newsletter\Actions\FindNewsletterAction;
use Modules\Newsletter\Actions\GetAllNewslettersAction;
use Modules\Newsletter\Actions\UpdateNewsletterAction;
use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Http\Requests\Api\Store;
use Modules\Newsletter\Http\Resources\NewsletterResource;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;
use ReflectionException;

class NewsletterController extends CoreController
{
    public function __construct(
        private readonly NewsletterRepository $repository,
        private readonly GetAllNewslettersAction $getAllAction,
        private readonly FindNewsletterAction $findAction,
        private readonly CreateNewsletterAction $createAction,
        private readonly UpdateNewsletterAction $updateAction,
        private readonly DeleteNewsletterAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Newsletter::class);

        return NewsletterResource::collection($this->getAllAction->execute());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Newsletter::class);

        $dto = NewsletterDTO::fromRequest($request);
        $newsletter = $this->createAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName(Newsletter::class),
            ]))
            ->respond(new NewsletterResource($newsletter));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $this->authorizeFromRepo(NewsletterRepository::class, 'view', $id);
        $newsletter = $this->findAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName(Newsletter::class),
            ]))
            ->respond(new NewsletterResource($newsletter));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Store $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(NewsletterRepository::class, 'update', $id);

        $dto = NewsletterDTO::fromRequest($request, $id);
        $newsletter = $this->updateAction->execute($dto);

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName(Newsletter::class),
            ]))
            ->respond(new NewsletterResource($newsletter));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(NewsletterRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName(Newsletter::class),
            ]))
            ->respond(null);
    }
}
