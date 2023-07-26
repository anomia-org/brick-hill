<?php

namespace App\Models\Item;

use App\Contracts\Models\IThumbnailable;
use App\Models\Polymorphic\Asset;
use App\Traits\Models\Polymorphic\Thumbnailable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Item\Version
 *
 * @property int $id
 * @property int $item_id
 * @property int $asset_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Asset $asset
 * @property-read mixed $items_list
 * @property-read \App\Models\Item\Item $item
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @property-read int|null $thumbnails_count
 * @method static \Database\Factories\Item\VersionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Version newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Version newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Version query()
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Version whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Thumbnail> $thumbnails
 * @mixin \Eloquent
 */
class Version extends Model implements IThumbnailable
{
    use Thumbnailable, HasFactory;

    /**
     * Columns that can be mass modified
     * 
     * @var string[]
     */
    protected $fillable = [
        'item_id', 'asset_id'
    ];

    /**
     * Returns the item the version belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Returns the single asset the version belongs to
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Returns if the Thumbnailable can have a Thumbnail
     * 
     * @return bool 
     */
    public function hasThumbnail(): bool
    {
        return true;
    }

    /**
     * Returns color and item data for the thumbnail to generate
     * 
     * @return array 
     */
    public function getThumbnailData(): array
    {
        return [];
    }
}
