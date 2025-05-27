<?php

declare(strict_types=1);

namespace Modules\Message\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'name' => 'string|nullable|min:2',
            'email' => 'email|nullable',
            'message' => 'nullable|min:20|max:200',
            'subject' => 'string|nullable',
            'phone' => 'string|nullable',
        ];
    }
}
