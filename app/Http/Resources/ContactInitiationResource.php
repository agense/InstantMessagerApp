<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ContactResource;

class ContactInitiationResource extends JsonResource
{
    private $message;

    public function __construct($resource, String $message = null) {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->message = $message; 
    }

    /**
     * Transform the resource into an array.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'message' => $this->when($this->message !== null, $this->message),
            'contact_request' => [
                'id' => $this->id,
                'from' => is_int($this->from) ? new ContactResource($this->getRelationValue('from')):  new ContactResource($this->from),
                'to' => is_int($this->to) ? new ContactResource($this->getRelationValue('to')):  new ContactResource($this->to),
                'status' => $this->status,
                'created_at' => $this->created_at !== null ? $this->created_at->format('Y-m-d h:s') : null
            ]
        ];
    }
}
