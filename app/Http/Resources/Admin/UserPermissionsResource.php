<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Permission\Role;

/**
 * @mixin \App\Models\User\User
 */
class UserPermissionsResource extends JsonResource
{
    use \App\Traits\Resources\Cursorable;

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
            'power' => $this->power,
            'roles' => $this->roles->map(fn (Role $role): int => $role->id)
        ];
    }
}
