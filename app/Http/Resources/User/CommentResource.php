<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Polymorphic\Comment
 */
class CommentResource extends JsonResource
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
            'author_id' => $this->author_id,
            'asset_id' => $this->commentable_id,
            'comment' => $this->comment,
            'scrubbed' => $this->scrubbed,
            'created_at' => $this->created_at,
            'author' => new UserResource($this->author)
        ];
    }
}
