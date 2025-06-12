<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\Core\Support\Media\MediaUploader;
use Modules\User\DTOs\UserDTO;
use Modules\User\Repository\UserRepository;

readonly class UpdateUserAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(int $id, UserDTO $dto): void
    {
        $this->repository->update($id, [
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => $dto->email_verified_at,
        ]);
        $user = $this->repository->find($id);
        // Optional photo upload using MediaUploader
        MediaUploader::uploadSingle($user, 'photo', 'photo');
    }
}
