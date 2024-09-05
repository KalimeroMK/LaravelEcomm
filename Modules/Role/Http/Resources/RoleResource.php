<?php

namespace Modules\Role\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Permission\Http\Resources\PermissionResource;
use Modules\Role\Models\Role;

/** @mixin Role */
class RoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $this->load('permissions');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
