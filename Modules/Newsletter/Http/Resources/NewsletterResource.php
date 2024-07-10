<?php

namespace Modules\Newsletter\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Newsletter\Models\Newsletter;

/** @mixin Newsletter */
class NewsletterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed> Array of various types depending on the property.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'token' => $this->token,
            'is_validated' => $this->is_validated,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
