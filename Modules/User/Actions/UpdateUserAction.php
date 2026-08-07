<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\Core\Support\Media\MediaUploader;
use Modules\User\DTOs\UserDTO;
use Modules\User\Repository\UserRepository;

readonly class UpdateUserAction
{
    public function __construct(private UserRepository $repository) {}

    public function execute(int $id, UserDTO $dto): \Modules\User\Models\User
    {
        // Only write attributes that were actually submitted. Passing the whole
        // DTO would blank out name/email/email_verified_at on a partial update,
        // silently un-verifying the account.
        $attributes = array_filter([
            'name' => $dto->name,
            'email' => $dto->email,
            'email_verified_at' => $dto->email_verified_at,
            // Hashed by UserObserver::updating() once the attribute is dirty.
            'password' => $dto->password,
        ], static fn (?string $value): bool => $value !== null && $value !== '');

        if ($attributes !== []) {
            $this->repository->update($id, $attributes);
        }

        $user = $this->repository->findById($id);
        // Optional photo upload using MediaUploader
        MediaUploader::uploadSingle($user, 'photo', 'photo');

        return $user->fresh();
    }
}
