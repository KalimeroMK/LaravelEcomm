<?php

namespace Modules\Attribute\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|unique:attributes,name,'.$this->route()->parameter('id'),
            'code' => 'nullable|unique:attributes,code,'.$this->route()->parameter('id'),
            'display' => 'sometimes|in:input,radio,color,button,select,checkbox,multiselect',
            'filterable' => 'sometimes|in:0,1',
            'configurable' => 'sometimes|in:0,1',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
