<?php

namespace App\Http\Resources\Game;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Set\ClientBuild
 */
class ClientBuildResource extends JsonResource
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
            'tag' => $this->tag,
            'created_at' => $this->created_at
        ];
    }
}
