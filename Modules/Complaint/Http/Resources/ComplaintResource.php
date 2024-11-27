<?php

namespace Modules\Complaint\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Complaint\Models\Complaint;
use Modules\Order\Http\Resources\OrderResource;
use Modules\User\Http\Resource\UserResource;

/** @mixin Complaint */
class ComplaintResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'replies_count' => $this->replies_count,
            'user' => new UserResource($this->whenLoaded('user')),
            'order' => new OrderResource($this->whenLoaded('order')),
        ];
    }
}
