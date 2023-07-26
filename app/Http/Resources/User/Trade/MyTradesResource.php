<?php

namespace App\Http\Resources\User\Trade;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Carbon\CarbonInterface;

/**
 * @mixin \App\Models\User\Trade
 */
class MyTradesResource extends JsonResource
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
            'is_accepted' => $this->is_accepted,
            'is_pending' => $this->is_pending,
            'is_cancelled' => $this->is_cancelled,
            'has_errored' => $this->has_errored,
            'updated_at' => $this->updated_at,
            'human_time' => Carbon::parse($this->updated_at)->diffForHumans(now(), CarbonInterface::DIFF_RELATIVE_TO_NOW, true),
            'user' => $this->when($this->receiver_id == $request->user->id, $this->sender, $this->receiver),
            'sender_avg' => $this->sender_avg,
            'receiver_avg' => $this->receiver_avg
        ];
    }
}
