<?php

namespace App\Http\Resources\Polymorphic;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Polymorphic\Tag
 */
class TagResource extends JsonResource
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
            'name' => $this->name
        ];
    }
}
