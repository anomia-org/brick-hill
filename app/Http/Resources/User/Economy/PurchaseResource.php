<?php

namespace App\Http\Resources\User\Economy;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\Item\ItemSmallResource;
use App\Http\Resources\User\UserResource;

/**
 * @mixin \App\Models\Economy\Purchase
 */
class PurchaseResource extends JsonResource
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
            'user_id' => $this->user_id,
            'seller_id' => $this->seller_id,
            'product_id' => $this->product_id,
            'price' => $this->price,
            'pay_id' => $this->pay_id,
            'created_at' => $this->created_at,
            'item' => new ItemSmallResource($this->product->productable),
            'user' => new UserResource($this->when($this->user_id == $request->user->id, $this->seller, $this->purchaser))
        ];
    }
}
