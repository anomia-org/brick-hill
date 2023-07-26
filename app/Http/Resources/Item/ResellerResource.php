<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User\UserResource;

/**
 * @mixin \App\Models\Item\SpecialSeller
 */
class ResellerResource extends JsonResource
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
            'crate_id' => $this->crate_id,
            'serial' => $this->whenLoaded('crate', fn () => $this->crate->serial),
            'user' => new UserResource($this->whenLoaded('user')),
            'bucks' => $this->bucks
        ];
    }
}
