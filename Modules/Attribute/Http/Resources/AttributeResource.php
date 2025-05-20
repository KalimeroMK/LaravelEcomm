<?php

namespace Modules\Attribute\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Models\Attribute;

/** @mixin Attribute */
class AttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'display' => $this->display,
            'is_required' => $this->is_required,
            'is_filterable' => $this->is_filterable,
            'is_configurable' => $this->is_configurable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'values_count' => $this->values_count,
            'filterable' => $this->filterable,
            'configurable' => $this->configurable,

            'attribute_group_id' => $this->attribute_group_id,
        ];
    }
}
