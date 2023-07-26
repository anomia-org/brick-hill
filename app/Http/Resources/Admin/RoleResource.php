<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Permission\Permission;

/**
 * @mixin \App\Models\Permission\Role
 */
class RoleResource extends JsonResource
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
            'permissions' => $this->permissions->map(fn (Permission $permission): array => ['id' => $permission->id, 'name' => $permission->name])
        ];
    }
}
