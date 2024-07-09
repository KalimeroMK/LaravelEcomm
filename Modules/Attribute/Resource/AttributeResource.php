<?php

namespace Modules\Attribute\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Models\Attribute;

/** @mixin Attribute */
class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>  // Mixed indicates that the array can contain multiple data types
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'display' => $this->display,
            'filterable' => $this->filterable,
            'configurable' => $this->configurable,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'values_count' => $this->when($this->values_count !== null, $this->values_count),
        ];
    }
}
