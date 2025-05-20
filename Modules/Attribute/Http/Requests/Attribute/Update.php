<?php

declare(strict_types=1);

namespace Modules\Attribute\Http\Requests\Attribute;

use Exception;
use Illuminate\Validation\Rule;
use Modules\Core\Http\Requests\CoreRequest;

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
        $attributeId = $this->route('attribute');

        return [
            'name' => ['sometimes', 'string', Rule::unique('attributes', 'name')->ignore($attributeId)],
            'code' => ['sometimes', 'string', Rule::unique('attributes', 'code')->ignore($attributeId)],
            'display' => ['sometimes', 'in:input,radio,color,button,select,checkbox,multiselect'],
            'filterable' => ['sometimes', 'boolean'],
            'configurable' => ['sometimes', 'boolean'],
        ];
    }
}
