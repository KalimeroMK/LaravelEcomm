<?php

declare(strict_types=1);

namespace Modules\User\Service;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Modules\Core\Service\CoreService;
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
     * Updates a user and syncs roles.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateWithRoles(int $id, array $data): Model
    {
        $input = $this->prepareInputData($data);
        $user = $this->user_repository->findById($id);
        $this->user_repository->update($id, $input);
        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles($data['roles'] ?? []);
        }

        return $user;
    }

    /**
     * Prepares input data for updating a user.
     *
     * @param  array<string, mixed>  $data  The data to prepare.
     * @return array<string, mixed> The prepared data.
     */
    private function prepareInputData(array $data): array
    {
        if (! empty($data['password'])) {
            return ['password' => Hash::make($data['password'])];
        }

        return Arr::except($data, ['password']);
    }
}
