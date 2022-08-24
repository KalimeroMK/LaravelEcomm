<?php

namespace Modules\Message\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\Core\Helpers\Helper;
use Modules\Message\Http\Resources\MessageResource;
use Modules\Message\Service\MessageService;

class MessageController extends Controller
{
    
    private MessageService $message_service;
    
    public function __construct(MessageService $message_service)
    {
        $this->message_service = $message_service;
    }
    
    /**
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        return MessageResource::collection($this->message_service->getAll());
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
                                $this->message_service->message_repository->model
                            ),
                        ]
                    )
                )
                ->respond(new MessageResource($this->message_service->show($id)));
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
                                $this->message_service->message_repository->model
                            ),
                        ]
                    )
                )
                ->respond($this->message_service->destroy($id));
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}
