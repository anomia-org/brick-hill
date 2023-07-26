<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Item\Series
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\Item> $items
 * @property-read int|null $items_count
 * @method static \Database\Factories\Item\SeriesFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Series newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Series newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Series query()
 * @method static \Illuminate\Database\Eloquent\Builder|Series whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Series whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Series whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Series whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Item\Item> $items
 * @mixin \Eloquent
 */
class Series extends Model
{
    use HasFactory;

    /**
     * Columns that can be mass modified
     * 
     * @var string[]
     */
    protected $fillable = [
        'name'
    ];

    /**
     * Return items associated with the series
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany 
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
