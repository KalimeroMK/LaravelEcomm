<?php

declare(strict_types=1);

namespace Modules\Role\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Update extends CoreRequest
{
    public function rules(): array
    {
        $roleId = $this->route('role')?->id ?? $this->route('id');

        return [
            'name' => 'required|string|max:50|unique:roles,name,'.$roleId,
            'guard_name' => 'nullable|string|max:255|in:web,api',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('guard_name')) {
            $this->merge(['guard_name' => 'web']);
        }
    }
}
