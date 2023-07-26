<?php

namespace App\Models\Polymorphic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Builder;

use App\Models\Item\Item;

use Carbon\Carbon;

/**
 * App\Models\Polymorphic\Thumbnail
 *
 * @property int $id
 * @property string $uuid
 * @property string $contents_uuid
 * @property mixed $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\Polymorphic\ThumbnailFactory factory($count = null, $state = [])
 * @method static Builder|Thumbnail newModelQuery()
 * @method static Builder|Thumbnail newQuery()
 * @method static Builder|Thumbnail notExpired()
 * @method static Builder|Thumbnail query()
 * @method static Builder|Thumbnail whereContentsUuid($value)
 * @method static Builder|Thumbnail whereCreatedAt($value)
 * @method static Builder|Thumbnail whereExpiresAt($value)
 * @method static Builder|Thumbnail whereId($value)
 * @method static Builder|Thumbnail whereUpdatedAt($value)
 * @method static Builder|Thumbnail whereUuid($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Item> $items
 * @mixin \Eloquent
 */
class Thumbnail extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'contents_uuid', 'expires_at'
    ];

    /**
     * Get all items associated with Thumbnail
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany 
     */
    public function items(): MorphToMany
    {
        return $this->morphedByMany(Item::class, 'thumbnailable');
    }

    /**
     * Scope for Thumbnails which have not passed their expiry time
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query 
     * @return \Illuminate\Database\Eloquent\Builder 
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', Carbon::now());
    }
}
