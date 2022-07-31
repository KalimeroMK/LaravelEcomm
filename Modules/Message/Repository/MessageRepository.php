<?php

namespace Modules\Message\Repository;

use Modules\Admin\Models\Message;
use Modules\Core\Repositories\Repository;

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