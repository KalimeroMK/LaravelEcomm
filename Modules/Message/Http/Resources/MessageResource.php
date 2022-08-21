<?php

namespace Modules\Message\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Message\Models\Message;

/** @mixin Message */
class MessageResource extends JsonResource
{
    /**
     * @param  Request  $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'subject'    => $this->subject,
            'email'      => $this->email,
            'photo'      => $this->photo,
            'phone'      => $this->phone,
            'message'    => $this->message,
            'read_at'    => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
