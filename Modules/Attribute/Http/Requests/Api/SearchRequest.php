<?php

namespace Modules\Attribute\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class SearchRequest extends CoreRequest
{
    /**
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string',
            'code' => 'nullable|unique:attributes,code|string',
            'type' => 'nullable|in:url,hex,text,date,time,float,string,integer,boolean,decimal|string',
            'display' => 'nullable|in:input,radio,color,button,select,checkbox,multiselect|string',
            'filterable' => 'nullable|in:0,1|boolean',
            'configurable' => 'nullable|in:0,1|boolean',
            'per_page' => 'nullable|int',
            'all_included' => 'nullable|boolean',
        ];
    }
}