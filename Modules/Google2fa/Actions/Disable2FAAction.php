<?php

declare(strict_types=1);

namespace Modules\Google2fa\Actions;

use Modules\Google2fa\Models\Google2fa;
use Modules\User\Models\User;

readonly class Disable2FAAction
{
    public function execute(User $user): Google2fa
    {
        $loginSecurity = Google2fa::firstOrNew(['user_id' => $user->id]);
        $loginSecurity->google2fa_enable = false;
        $loginSecurity->save();

        return $loginSecurity;
    }
}
