<?php

namespace Modules\Attribute\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'code' => 'required|unique:attributes,code',
            'type' => 'sometimes|in:url,hex,text,date,time,float,string,integer,boolean,decimal',
            'display' => 'sometimes|in:input,radio,color,button,select,checkbox,multiselect',
            'filterable' => 'sometimes|in:0,1',
            'configurable' => 'sometimes|in:0,1',
        ];
    }
}