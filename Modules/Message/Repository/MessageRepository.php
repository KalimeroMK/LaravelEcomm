<?php

namespace Modules\Message\Repository;

use Modules\Core\Repositories\Repository;
use Modules\Message\Models\Message;

class MessageRepository extends Repository
{
    public $model = Message::class;
    
    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::paginate(10);
    }
}