<?php

namespace App\Http\Resources\Admin\Shop;

use App\Http\Resources\Item\EventResource;
use App\Http\Resources\Item\ItemResource;
use App\Http\Resources\Polymorphic\TypeResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin \App\Models\Item\ItemSchedule
 */
class ItemScheduleResource extends JsonResource
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
            'item' => new ItemResource($this->item),
            'user' => new UserResource($this->user),
            'approver' => new UserResource($this->approver),
            'is_approved' => $this->is_approved,
            'hide_update' => $this->hide_update,
            'can_overwrite' => Auth::user()->can('update', $this->approver),
            'type' => new TypeResource($this->itemType),
            'series_id' => $this->series_id,
            'event' => new EventResource($this->event),
            'scheduled_for' => $this->scheduled_for,
            'name' => $this->name,
            'description' => $this->description,
            'bits' => $this->bits,
            'bucks' => $this->bucks,
            'timer' => $this->timer,
            'timer_date' => $this->timer_date,
            'special_edition' => $this->special_edition,
            'special' => $this->special,
            'stock' => $this->special_q,
            'human_time' => \Helper::time_elapsed_string($this->scheduled_for),
        ];
    }
}
