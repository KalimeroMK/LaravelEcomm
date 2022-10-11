<?php

namespace Modules\Attribute\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'         => 'required',
            'code'         => 'required|unique:attributes,code,' . $this->attribute->id,
            'display'      => 'sometimes|in:input,radio,color,button,select,checkbox,multiselect',
            'filterable'   => 'sometimes|in:0,1',
            'configurable' => 'sometimes|in:0,1',
        ];
    }
    
    public function authorize(): bool
    {
        return true;
    }
}