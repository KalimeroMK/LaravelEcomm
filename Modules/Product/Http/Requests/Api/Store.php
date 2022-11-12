<?php

namespace Modules\Product\Http\Requests\Api;

use Modules\Core\Http\Requests\Api\CoreRequest;

class Store extends CoreRequest
{
    public function rules(): array
    {
        return [
            'title'        => 'string|required',
            'summary'      => 'string|required',
            'description'  => 'string|nullable',
            'photo'        => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
            'size'         => 'sometimes|array',
            'size.*'       => 'required|exists:sizes,id',
            'color'        => 'required',
            'category'     => 'sometimes|array',
            'category.*'   => 'required|exists:categories,id',
            'tag'          => 'sometimes|array',
            'tag.*'        => 'required|exists:tags,id',
            'condition_id' => 'required|exists:conditions,id',
            'stock'        => "required|numeric",
            'brand_id'     => 'nullable|exists:brands,id',
            'is_featured'  => 'sometimes|in:1',
            'status'       => 'required|in:active,inactive',
            'price'        => 'required|numeric',
            'discount'     => 'nullable|numeric',
        ];
    }
}
