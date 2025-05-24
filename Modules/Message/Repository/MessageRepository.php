<?php

declare(strict_types=1);

namespace Modules\Message\Repository;

use Illuminate\Support\Collection;
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
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->get();
    }
}
