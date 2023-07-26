<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User\User
 * 
 * @property int $is_verified_designer
 * @property string $include_last_online
 */
class UserResource extends JsonResource
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
            'username' => $this->username,
            'last_online' => $this->when(isset($this->include_last_online), $this->include_last_online),
            'avatar_hash' => $this->when(isset($this->avatar_hash), $this->avatar_hash),
            'is_verified_designer' => $this->when(isset($this->is_verified_designer), $this->is_verified_designer)
        ];
    }
}
