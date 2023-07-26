<?php

namespace App\Contracts\Models;

use App\Constants\Thumbnails\ThumbnailState;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property int $id
 * @property \Illuminate\Support\Collection $items_list
 * @property-read  \Illuminate\Database\Eloquent\Collection|\App\Models\Polymorphic\Thumbnail[] $thumbnails
 */
interface IThumbnailable
{
    /**
     * Returns if the Thumbnailable can have a Thumbnail
     * 
     * @return bool 
     */
    public function hasThumbnail(): bool;

    /**
     * Returns color and item data for the thumbnail to generate
     * 
     * @return array 
     */
    public function getThumbnailData(): array;

    /**
     * Overwrite the State returned by the Thumbnail API
     * 
     * @return \App\Constants\Thumbnails\ThumbnailState|bool 
     */
    public function overwriteThumbnailState(): ThumbnailState | bool;

    /**
     * Return associated Thumbnails
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany 
     */
    public function thumbnails(): MorphToMany;

    /**
     * Returns a list of unique item ids the thumbnail contains
     * 
     * @return \Illuminate\Support\Collection<int, mixed>
     */
    public function getItemsListAttribute(): \Illuminate\Support\Collection;

    /**
     * Show to PHPStan that the class has a getter instead of using AllowDynamicProperties
     * I can't seem to find a better way to enforce that this interface is a model with the parameters necessary, so this is the best I can get for now
     * 
     * @param mixed $key 
     * @return mixed 
     */
    public function __get($key);

    /**
     * @param mixed $key 
     * @param mixed $value 
     * @return mixed 
     */
    public function __set($key, $value);
}
