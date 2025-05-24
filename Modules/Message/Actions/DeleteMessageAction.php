<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Modules\Message\Repository\MessageRepository;

readonly class DeleteMessageAction
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
