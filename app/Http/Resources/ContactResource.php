<?php

namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'profile_image' => $this->getProfileImageUrl(),
            $this->mergeWhen( isset($this->messages_sent_count), ['unread_messages_count' => $this->messages_sent_count])
        ];
    }
}
