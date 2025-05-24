<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Illuminate\Support\Collection;
use Modules\Message\Repository\MessageRepository;

readonly class GetAllMessagesAction
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function execute(): Collection
    {
        return $this->repository->findAll();
    }
}
