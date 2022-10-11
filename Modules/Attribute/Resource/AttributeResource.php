<?php

namespace Modules\Attribute\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Attribute\Models\Attribute;

/** @mixin Attribute */
class AttributeResource extends JsonResource
{
    /**
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'code'         => $this->code,
            'type'         => $this->type,
            'display'      => $this->display,
            'filterable'   => $this->filterable,
            'configurable' => $this->configurable,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
            'values_count' => $this->values_count,
        ];
    }
}
