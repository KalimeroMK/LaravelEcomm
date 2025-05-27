<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Illuminate\Support\Collection;
use Modules\Message\Repository\MessageRepository;

class GetAllMessagesAction
{
    private MessageRepository $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
