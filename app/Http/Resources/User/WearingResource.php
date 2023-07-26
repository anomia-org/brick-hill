<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Item\ItemSmallResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WearingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'colors' => $this['colors'],
            'raw_items' => $this['items'],
            'items' => ItemSmallResource::collection($this['item_models'])
        ];
    }
}
