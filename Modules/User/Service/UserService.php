<?php

namespace Modules\User\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Service\CoreService;
use Modules\User\Models\User;
use Modules\User\Repository\UserRepository;

class UserService extends CoreService
{
    private UserRepository $user_repository;

    public function __construct(UserRepository $user_repository)
    {
        parent::__construct($user_repository);
        $this->user_repository = $user_repository;
    }


    /**
     * @param $request
     *
     * @return void
     */
    public function register($request): void
    {
        $input = $request->all();
        $user = User::create($input);
        $user->assignRole($request->input('roles'));
    }

    /**
     * @param $id
     * @param $data
     *
     * @return Model
     */
    public function update($id, $data): Model
    {
        $input = $this->prepareInputData($data);
        $user = $this->user_repository->findById($id);
        $this->user_repository->update($id, $input);
        $user->syncRoles($data['roles']);

        return $user;
    }

    private function prepareInputData($data): array
    {
        return !empty($data['password']) ? ['password' => Hash::make($data['password'])] : Arr::except($data,
            ['password']);
    }


}
