<?php

namespace Modules\User\Repository;

use Modules\Core\Repositories\Repository;
use Modules\User\Models\User;

class UserRepository extends Repository
{
    public \Illuminate\Database\Eloquent\Model $model = User::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::with('roles')->orderBy('id', 'DESC')->get();
    }

}