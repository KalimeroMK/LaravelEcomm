<?php

declare(strict_types=1);

namespace Modules\User\Http\Requests\Password;

use App\Rules\MatchOldPassword;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
