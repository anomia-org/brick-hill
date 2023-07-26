<?php

namespace App\Traits\Models\Polymorphic;

use App\Traits\Models\Polymorphic;

use App\Models\Polymorphic\Comment;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Commentable
{
    use Polymorphic;

    /**
     * Returns comments related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Comment>
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
