<?php

declare(strict_types=1);

namespace Modules\Message\Repository;

use Illuminate\Database\Eloquent\Collection;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Message\Models\Message;

class MessageRepository extends EloquentRepository implements EloquentRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Message::class);
    }

    /**
     * Get all messages.
     *
     * @return Collection<int, Message>
     */
    public function findAll(): Collection
    {
        return Message::orderBy('id', 'desc')->get();
    }
}
