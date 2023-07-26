<?php

namespace App\Traits\Models\Polymorphic;

use App\Traits\Models\Polymorphic;

use App\Models\Polymorphic\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{
    use Polymorphic;

    /**
     * Returns associated Tags
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Tag>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
