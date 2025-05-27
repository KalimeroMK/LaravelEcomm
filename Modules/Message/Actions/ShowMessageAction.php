<?php

declare(strict_types=1);

namespace Modules\Message\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Message\Repository\MessageRepository;

class ShowMessageAction
{
    private MessageRepository $repository;

    public function __construct(MessageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): Model
    {
        return $this->repository->findById($id);
    }
}
