<?php

namespace Api\Resource;

use Hyperf\Resource\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'userinfo' => new UserResource($this['userinfo']),
            'token' => $this['token'],
        ];
    }
}
