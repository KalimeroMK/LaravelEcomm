<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Message\Repository\MessageRepository;

readonly class ShowMessageAction
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
