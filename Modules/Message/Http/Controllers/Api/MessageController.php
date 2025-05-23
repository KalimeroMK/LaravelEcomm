<?php

declare(strict_types=1);

namespace Modules\Message\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Message\Actions\CreateMessageAction;
use Modules\Message\Actions\DeleteMessageAction;
use Modules\Message\Actions\GetAllMessagesAction;
use Modules\Message\Actions\ShowMessageAction;
use Modules\Message\Actions\UpdateMessageAction;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Message\Http\Resources\MessageResource;
use ReflectionException;

class MessageController extends CoreController
{
    public function __construct()
    {
        $this->middleware('permission:message-list', ['only' => ['index']]);
        $this->middleware('permission:message-show', ['only' => ['show']]);
        $this->middleware('permission:message-create', ['only' => ['store']]);
        $this->middleware('permission:message-edit', ['only' => ['update']]);
        $this->middleware('permission:message-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        $messagesDto = (new GetAllMessagesAction())->execute();

        return MessageResource::collection($messagesDto->messages);
    }

    /**
     * @throws ReflectionException
     */
    public function store(Store $request): JsonResponse
    {
        $messageDto = (new CreateMessageAction())->execute($request->validated());

        return $this->setMessage(__('apiResponse.storeSuccess', ['resource' => 'Message']))->respond(new MessageResource($messageDto));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): JsonResponse
    {
        $messageDto = (new ShowMessageAction())->execute($id);

        return $this->setMessage(__('apiResponse.ok', ['resource' => 'Message']))->respond(new MessageResource($messageDto));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Store $request, int $id): JsonResponse
    {
        $messageDto = (new UpdateMessageAction())->execute($id, $request->validated());

        return $this->setMessage(__('apiResponse.updateSuccess', ['resource' => 'Message']))->respond(new MessageResource($messageDto));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): JsonResponse
    {
        (new DeleteMessageAction())->execute($id);

        return $this->setMessage(__('apiResponse.deleteSuccess', ['resource' => 'Message']))->respond(null);
    }
}
