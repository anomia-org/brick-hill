<?php

namespace App\Models\Set;

use Illuminate\Database\Eloquent\Model;

use App\Traits\Models\Economy\Productable;
use App\Traits\Models\Polymorphic\Assetable;

/**
 * App\Models\Set\SetPass
 *
 * @property int $id
 * @property int $set_id
 * @property string $name
 * @property string|null $description
 * @property int $scrubbed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Crate> $crates
 * @property-read int|null $crates_count
 * @property-read \App\Models\Polymorphic\Asset|null $latestAsset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Crate> $owners
 * @property-read int|null $owners_count
 * @property-read \App\Models\Economy\Product|null $product
 * @property-read \App\Models\Set\Set $set
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass query()
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereScrubbed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereSetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SetPass whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Polymorphic\Asset> $assets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Crate> $crates
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Crate> $owners
 * @mixin \Eloquent
 */
class SetPass extends Model
{
    use Productable, Assetable;

    public function isPurchasable(): bool
    {
        return true;
    }

    public function isTradeable(): bool
    {
        return false;
    }

    /**
     * Returns the owning Set
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function set()
    {
        return $this->belongsTo(Set::class);
    }
}
