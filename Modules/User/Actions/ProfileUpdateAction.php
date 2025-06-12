<?php

declare(strict_types=1);

namespace Modules\User\Actions;

use Modules\Core\Support\Media\MediaUploader;
use Modules\User\DTOs\UserDTO;
use Modules\User\Models\User;

readonly class ProfileUpdateAction
{
    public function execute(User $user, UserDTO $dto): bool
    {
        MediaUploader::uploadSingle($user, 'photo', 'photo');

        return $user->fill([
            'name' => $dto->name,
            'email' => $dto->email,
        ])->save();
    }
}
