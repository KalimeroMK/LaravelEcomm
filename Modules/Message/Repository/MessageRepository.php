<?php

namespace Modules\Message\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\Message\Models\Message;

class MessageRepository extends Repository
{
    /**
     * @var string
     */
    public $model = Message::class;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::get();
    }
}
