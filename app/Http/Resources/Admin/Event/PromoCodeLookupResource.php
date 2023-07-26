<?php

namespace App\Http\Resources\Admin\Event;

use App\Http\Resources\Item\ItemSmallResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Event\PromoCode
 */
class PromoCodeLookupResource extends JsonResource
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
            'is_single_use' => $this->is_single_use,
            'is_redeemed' => $this->is_redeemed,
            'redeemed_by' => new UserResource($this->redeemedBy),
            'expires_at' => $this->expires_at,
        ];
    }
}
