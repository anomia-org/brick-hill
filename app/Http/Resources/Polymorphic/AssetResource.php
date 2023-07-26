<?php

namespace App\Http\Resources\Polymorphic;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Polymorphic\Asset
 */
class AssetResource extends JsonResource
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
            'creator_id' => $this->creator_id,
            'url' => $this->url,
            'is_approved' => $this->is_approved,
            'active' => $this->is_selected_version,
            'created_at' => $this->created_at
        ];
    }
}
