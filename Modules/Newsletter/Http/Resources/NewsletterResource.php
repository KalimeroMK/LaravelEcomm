<?php

namespace Modules\Newsletter\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Newsletter\Models\Newsletter;

/** @mixin Newsletter */
class NewsletterResource extends JsonResource
{
    /**
     * @return string[]
     */
    public function toArray($request): array
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
