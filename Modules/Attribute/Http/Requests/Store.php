<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests;

use Modules\Core\Http\Requests\CoreRequest;

class Store extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:50|unique:brands,name',
            'code' => 'required|unique:attributes,code',
            'type' => 'sometimes|in:url,hex,text,date,time,float,string,integer,boolean,decimal',
            'display' => 'sometimes|in:input,radio,color,button,select,checkbox,multiselect',
            'filterable' => 'sometimes|in:0,1',
            'configurable' => 'sometimes|in:0,1',
        ];
    }
}
