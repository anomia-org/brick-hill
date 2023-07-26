<?php

namespace App\Traits\Models\Polymorphic;

use App\Traits\Models\Polymorphic;

use App\Models\Polymorphic\Favorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Favoriteable
{
    use Polymorphic;

    /**
     * Returns favorites related to the model
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany<Favorite>
     */
    public function favorites(): MorphMany
    {
        return $this->morphMany(Favorite::class, 'favoriteable');
    }
}
