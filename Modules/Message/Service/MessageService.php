<?php

namespace Modules\Message\Service;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Core\Service\CoreService;
use Modules\Message\Repository\MessageRepository;

class MessageService extends CoreService
{
    public MessageRepository $message_repository;

    public function __construct(MessageRepository $message_repository)
    {
        parent::__construct($message_repository);
        $this->message_repository = $message_repository;
    }

    /**
     * Show details of an attribute.
     *
     * @param int $id The attribute ID.
     * @return Model|null
     */
    public function show(int $id): ?Model
    {
        $message = $this->message_repository->findById($id);
        $message->read_at = Carbon::now();
        $message->save();

        return $message;
    }
}
