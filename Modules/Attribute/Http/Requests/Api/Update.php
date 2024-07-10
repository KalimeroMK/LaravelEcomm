<?php

namespace Modules\Attribute\Http\Requests\Api;

use Exception;
use Illuminate\Validation\Rule;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Http\Requests\Api\CoreRequest;

class Update extends CoreRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function rules(): array
    {
        $attribute = $this->route('attribute');

        // Ensure that the attribute is indeed an Attribute model instance
        if ($attribute instanceof Attribute) {
            $attributeId = $attribute->id;
        } else {
            // Handle the error appropriately, maybe log it or throw an exception
            throw new Exception('Expected an instance of Attribute, received '.gettype($attribute));
        }

        return [
            'name' => ['sometimes', 'string', Rule::unique('attributes', 'name')->ignore($attributeId)],
            'code' => ['sometimes', 'string', Rule::unique('attributes', 'code')->ignore($attributeId)],
            'display' => ['sometimes', 'in:input,radio,color,button,select,checkbox,multiselect'],
            'filterable' => ['sometimes', 'boolean'],
            'configurable' => ['sometimes', 'boolean'],
        ];
    }
}
