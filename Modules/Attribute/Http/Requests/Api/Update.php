<?php

namespace Modules\Attribute\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{

    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'code' => 'required|unique:attributes,code,' . $this->route()->id,
            'display' => 'sometimes|in:input,radio,color,button,select,checkbox,multiselect',
            'filterable' => 'sometimes|in:0,1',
            'configurable' => 'sometimes|in:0,1',
        ];
    }
}
