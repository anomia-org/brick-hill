<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Polymorphic\{
    TagResource,
    TypeResource
};

/**
 * @mixin \App\Models\Item\Item
 */
class ItemResource extends JsonResource
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
            'creator' => new UserResource($this->whenLoaded('creator')),
            'type' => new TypeResource($this->itemType),
            'tags' => $this->whenLoaded('tags', fn () => TagResource::collection($this->tags)),
            'event' => new EventResource($this->event),
            'series_id' => $this->series_id,
            'is_public' => $this->is_public,
            'name' => $this->name,
            'description' => $this->description,
            'bits' => $this->bits,
            'bucks' => $this->bucks,
            'offsale' => $this->offsale,
            'special_edition' => $this->special_edition,
            'special' => $this->special,
            'stock' => $this->special_q,
            'timer' => $this->timer,
            'timer_date' => $this->timer_date,
            $this->mergeWhen($this->is_special, [
                'average_price' => $this->average_price
            ]),
            $this->mergeWhen($this->special_edition, [
                'stock_left' => $this->stock_left
            ]),
            $this->mergeWhen($this->is_special, [
                'cheapest_seller' => new ResellerResource($this->cheapestPrivateSeller)
            ]),
            'thumbnail' => $this->thumbnail,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
