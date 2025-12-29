<?php

declare(strict_types=1);

namespace Modules\Message\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Message\Actions\CreateMessageAction;
use Modules\Message\Actions\DeleteMessageAction;
use Modules\Message\Actions\GetAllMessagesAction;
use Modules\Message\Actions\MarkAsReadAction;
use Modules\Message\Actions\MarkMultipleAsReadAction;
use Modules\Message\Actions\ReplyToMessageAction;
use Modules\Message\Actions\ShowMessageAction;
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
        private readonly GetAllMessagesAction $getAllAction,
        private readonly ShowMessageAction $showAction,
        private readonly CreateMessageAction $createAction,
        private readonly UpdateMessageAction $updateAction,
        private readonly DeleteMessageAction $deleteAction,
        private readonly MarkAsReadAction $markAsReadAction,
        private readonly ReplyToMessageAction $replyAction,
        private readonly MarkMultipleAsReadAction $markMultipleAsReadAction
    ) {}

    public function index(): ResourceCollection
    {
        $this->authorize('viewAny', Message::class);

        return MessageResource::collection($this->getAllAction->execute());
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
        $this->authorizeFromRepo(MessageRepository::class, 'view', $id);
        $message = $this->showAction->execute($id);

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

    /**
     * Mark message as read
     *
     * @throws ReflectionException
     */
    public function markAsRead(int $id): JsonResponse
    {
        $message = $this->authorizeFromRepo(MessageRepository::class, 'update', $id);
        $this->markAsReadAction->execute($message);

        return $this
            ->setMessage('Message marked as read successfully.')
            ->respond(new MessageResource($message->fresh()));
    }

    /**
     * Reply to message
     *
     * @throws ReflectionException
     */
    public function reply(int $id, Request $request): JsonResponse
    {
        $message = $this->authorizeFromRepo(MessageRepository::class, 'update', $id);

        $replyData = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        $reply = $this->replyAction->execute($message, $replyData);

        return $this
            ->setMessage('Reply sent successfully!')
            ->respond(new MessageResource($reply));
    }

    /**
     * Mark multiple messages as read
     */
    public function markMultipleAsRead(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Message::class);

        $messageIds = $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'integer|exists:messages,id',
        ])['message_ids'];

        $this->markMultipleAsReadAction->execute($messageIds);

        return $this
            ->setMessage('Messages marked as read!')
            ->respond(['count' => count($messageIds)]);
    }
}
