<?php

namespace Modules\Message\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Message\Http\Resources\MessageResource;
use Modules\Message\Service\MessageService;
use ReflectionException;

class MessageController extends CoreController
{
    private MessageService $message_service;

    public function __construct(MessageService $message_service)
    {
        $this->message_service = $message_service;
        $this->middleware('permission:message-list', ['only' => ['index']]);
        $this->middleware('permission:message-show', ['only' => ['show']]);
        $this->middleware('permission:message-create', ['only' => ['store']]);
        $this->middleware('permission:message-edit', ['only' => ['update']]);
        $this->middleware('permission:message-delete', ['only' => ['destroy']]);
    }

    public function index(): ResourceCollection
    {
        return MessageResource::collection($this->message_service->getAll());
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
                            $this->message_service->message_repository->model
                        ),
                    ]
                )
            )
            ->respond(new MessageResource($this->message_service->create($request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.ok',
                    [
                        'resource' => Helper::getResourceName(
                            $this->message_service->message_repository->model
                        ),
                    ]
                )
            )
            ->respond(new MessageResource($this->message_service->show($id)));
    }

    /**
     * @throws ReflectionException
     */
    public function update(Store $request, int $id): \Illuminate\Http\JsonResponse
    {
        return $this
            ->setMessage(
                __(
                    'apiResponse.updateSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->message_service->message_repository->model
                        ),
                    ]
                )
            )
            ->respond(new MessageResource($this->message_service->update($id, $request->validated())));
    }

    /**
     * @throws ReflectionException
     */
    public function destroy(int $id): \Illuminate\Http\JsonResponse
    {
        $this->message_service->delete($id);

        return $this
            ->setMessage(
                __(
                    'apiResponse.deleteSuccess',
                    [
                        'resource' => Helper::getResourceName(
                            $this->message_service->message_repository->model
                        ),
                    ]
                )
            )
            ->respond(null);
    }
}
