<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Item\Item
 */
class PendingItemResource extends JsonResource
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
            'creator_id' => $this->creator_id,
            'name' => $this->name,
            'description' => $this->description,
            'asset' => $this->when(!is_null($this->latestAsset), [
                'id' => $this->latestAsset?->id,
                'uuid' => $this->latestAsset?->uuid
            ])
        ];
    }
}
