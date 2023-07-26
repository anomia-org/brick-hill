<?php

namespace App\Traits\Models\Polymorphic;

use App\Traits\Models\Polymorphic;
use App\Constants\Thumbnails\ThumbnailState;

use App\Models\Polymorphic\Thumbnail;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Thumbnailable
{
    use Polymorphic;

    /**
     * Overwrite the State returned by the Thumbnail API
     * 
     * @return \App\Constants\Thumbnails\ThumbnailState|bool 
     */
    public function overwriteThumbnailState(): ThumbnailState | bool
    {
        return false;
    }

    /**
     * Returns associated Thumbnails
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Thumbnail>
     */
    public function thumbnails(): MorphToMany
    {
        return $this->morphToMany(Thumbnail::class, 'thumbnailable');
    }

    /**
     * Returns a list of unique item ids the thumbnail contains
     * 
     * @return \Illuminate\Support\Collection<int, mixed> 
     */
    public function getItemsListAttribute(): \Illuminate\Support\Collection
    {
        return collect(
            $this
                ->getThumbnailData()['items']
        )
            ->flatten()
            ->unique()
            // removes all falsey values (which can only be 0)
            ->filter();
    }
}
