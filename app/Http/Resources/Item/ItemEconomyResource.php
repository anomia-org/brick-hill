<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Item\Item
 */
class ItemEconomyResource extends JsonResource
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
            'thumbnail' => $this->thumbnail,
            'is_special' => $this->is_special,
            'type_id' => $this->type_id,
            'average_price' => $this->when($this->is_special, $this->average_price),
            'average_price_abbr' => $this->when($this->is_special, $this->average_price_abbr)
        ];
    }
}
