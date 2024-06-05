<?php

namespace Modules\User\Repository;

use Illuminate\Support\Collection;
use Modules\Core\Repositories\Repository;
use Modules\User\Models\User;

class UserRepository extends Repository
{
    public $model = User::class;

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->model::with('roles')->orderBy('id', 'DESC')->get();
    }

}