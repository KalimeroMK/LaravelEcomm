<?php

namespace Modules\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as BaseFormRequest;

class CoreRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> Array of field rules.
     */
    public function rules(): array
    {
        return [

        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
