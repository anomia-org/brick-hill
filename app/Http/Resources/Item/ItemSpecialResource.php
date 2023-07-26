<?php

namespace App\Http\Resources\Item;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin \App\Models\Item\Item
 */
class ItemSpecialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $cacheKey = "shop" . $this->id . "specialData";
        return [
            'last_calculated' => Cache::get($cacheKey . "lastCalculated"),
            'active_copies' => (int) Cache::get($cacheKey . "activeCopies"),
            'unique_owners' => (int) Cache::get($cacheKey . "uniqueOwners"),
            'volume_hoarded' => (int) Cache::get($cacheKey . "volumeHoarded"),
            'daily_views' => $this->daily_views,
            'avg_price' => $this->average_price,
            'seller_listings' => $this->privateSellers()->count(),
            // wrap in closure to prevent execution
            $this->mergeWhen(Auth::check(), fn () => [
                'you_own' => $this->crates()->ownedBy(Auth::id())->count(),
                'active_buy_requests' => Auth::user()->buyRequests()->active()->count()
            ])
        ];
    }
}
