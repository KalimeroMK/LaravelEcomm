<?php

namespace Modules\Post\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class ImportRequest extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string|array<string>>
     */
    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xlsx, csv, xls',
        ];
    }
}
