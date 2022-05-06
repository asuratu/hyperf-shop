<?php

namespace Api\Resource;

use Hyperf\Resource\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'province' => $this->province,
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'zip' => $this->zip,
            'contact_name' => $this->contact_name,
            'contact_phone' => $this->contact_phone,
            'last_used_at' => $this->last_used_at,
        ];
    }
}
