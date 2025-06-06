<?php

declare(strict_types=1);

namespace Modules\Message\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Message\Actions\CreateMessageAction;
use Modules\Message\Actions\DeleteMessageAction;
use Modules\Message\Actions\UpdateMessageAction;
use Modules\Message\DTOs\MessageDTO;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Message\Http\Requests\Api\Update;
use Modules\Message\Http\Resources\MessageResource;
use Modules\Message\Models\Message;
use Modules\Message\Repository\MessageRepository;
use ReflectionException;

class MessageController extends CoreController
{
    public function __construct(
        private readonly MessageRepository $repository,
        private readonly CreateMessageAction $createAction,
        private readonly UpdateMessageAction $updateAction,
        private readonly DeleteMessageAction $deleteAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Message::class);

        return MessageResource::collection($this->repository->findAll());
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $this->authorize('create', Message::class);

        $dto = MessageDTO::fromRequest($request);
        $message = $this->createAction->execute($dto);

        MediaUploader::uploadMultiple($message, ['images'], 'photo');

        return $this
            ->setMessage(__('apiResponse.storeSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new MessageResource($message->fresh('media')));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $message = $this->authorizeFromRepo(MessageRepository::class, 'view', $id);

        return $this
            ->setMessage(__('apiResponse.ok', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new MessageResource($message));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Update $request, int $id): JsonResponse
    {
        $this->authorizeFromRepo(MessageRepository::class, 'update', $id);

        $dto = MessageDTO::fromRequest($request, $id, $this->repository->findById($id));
        $message = $this->updateAction->execute($dto);
        /**
         * @var Message $message
         */
        MediaUploader::clearAndUpload($message, ['images'], 'messages');

        return $this
            ->setMessage(__('apiResponse.updateSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(new MessageResource($message->fresh('media')));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->authorizeFromRepo(MessageRepository::class, 'delete', $id);

        $this->deleteAction->execute($id);

        return $this
            ->setMessage(__('apiResponse.deleteSuccess', [
                'resource' => Helper::getResourceName($this->repository->modelClass),
            ]))
            ->respond(null);
    }
}
