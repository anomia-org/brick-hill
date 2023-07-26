<?php

namespace App\Http\Resources\Event;

use App\Http\Resources\Item\ItemSmallResource;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Event\PromoCode
 */
class PromoCodeResource extends JsonResource
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
            'item' => new ItemSmallResource($this->item),
            'leaving_soon' => !is_null($this->expires_at) && $this->expires_at <= Carbon::now()->addWeek(),
            'coming_soon' => !$this->item->is_public
        ];
    }
}
