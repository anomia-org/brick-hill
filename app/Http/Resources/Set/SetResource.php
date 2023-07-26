<?php

namespace App\Http\Resources\Set;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\Polymorphic\{
    AssetResource,
    TypeResource
};

/**
 * @mixin \App\Models\Set\Set
 */
class SetResource extends JsonResource
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
            'creator' => new UserResource($this->creator),
            'name' => $this->name,
            'description' => $this->description,
            'playing' => $this->playing,
            'visits' => $this->visits,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_featured' => $this->is_featured,
            'thumbnail' => new AssetResource($this->thumbnailAsset),
            'genre' => new TypeResource($this->setGenre),
        ];
    }
}
