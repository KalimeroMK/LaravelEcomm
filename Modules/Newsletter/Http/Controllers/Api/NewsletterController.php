<?php

declare(strict_types=1);

namespace Modules\Newsletter\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Newsletter\Actions\CreateNewsletterAction;
use Modules\Newsletter\Actions\DeleteNewsletterAction;
use Modules\Newsletter\Actions\GetAllNewslettersAction;
use Modules\Newsletter\DTOs\NewsletterDTO;
use Modules\Newsletter\Http\Requests\Api\Store;
use Modules\Newsletter\Http\Resources\NewsletterResource;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Repository\NewsletterRepository;
use ReflectionException;

class NewsletterController extends CoreController
{
    public function __construct(
        private readonly GetAllNewslettersAction $getAllAction,
        private readonly CreateNewsletterAction $createAction,
        private readonly DeleteNewsletterAction $deleteAction
    ) {
        // Permissions removed — using policies instead
    }

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Newsletter::class);
        $newslettersDto = $this->getAllAction->execute();
        return NewsletterResource::collection($newslettersDto->newsletters);
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
            ->setMessage(__('apiResponse.storeSuccess', ['resource' => Helper::getResourceName(Newsletter::class)]))
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
            ->setMessage(__('apiResponse.deleteSuccess', ['resource' => Helper::getResourceName(Newsletter::class)]))
            ->respond(null);
    }
}
