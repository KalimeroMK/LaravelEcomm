<?php

namespace Modules\User\Repository;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Repositories\Repository;
use Modules\User\Models\User;

class UserRepository extends Repository
{
    public Model $model = User::class;

    /**
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::with('roles')->orderBy('id', 'DESC')->get();
    }

}