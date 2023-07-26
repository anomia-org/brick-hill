<?php

namespace App\Http\Resources\User\Economy;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User\TradeValue
 */
class ValueResource extends JsonResource
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
            'user_id' => $this->user_id,
            'value' => $this->value,
            'direction' => $this->direction,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at
        ];
    }
}
