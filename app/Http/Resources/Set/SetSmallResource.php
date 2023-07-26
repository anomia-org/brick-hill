<?php

namespace App\Http\Resources\Set;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User\UserResource;

/**
 * @mixin \App\Models\Set\Set
 */
class SetSmallResource extends JsonResource
{
    use \App\Traits\Resources\Cursorable;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'creator' => new UserResource($this->creator),
            'name' => $this->name,
            'playing' => $this->playing,
            'thumbnail' => config('site.storage.domain') . $this->thumbnail,
        ];
    }
}
