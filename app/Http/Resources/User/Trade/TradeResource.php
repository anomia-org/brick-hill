<?php

namespace App\Http\Resources\User\Trade;

use App\Http\Resources\User\Economy\ValueResource;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\CrateResource;

/**
 * @mixin \App\Models\User\Trade
 */
class TradeResource extends JsonResource
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
            'trade' => [
                [
                    'user' => new UserResource($this->sender),
                    'user_value' => new ValueResource($this->sender->tradeValue),
                    'bucks' => $this->sender_bucks,
                    'items' => CrateResource::collection($this->senderItems)
                ],
                [
                    'user' => new UserResource($this->receiver),
                    'user_value' => new ValueResource($this->receiver->tradeValue),
                    'bucks' => $this->receiver_bucks,
                    'items' => CrateResource::collection($this->receiverItems)
                ]
            ],
            'is_accepted' => $this->is_accepted,
            'is_pending' => $this->is_pending,
            'is_cancelled' => $this->is_cancelled,
            'has_errored' => $this->has_errored,
            'created_at' => $this->created_at,
        ];
    }
}
