<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Item\HistoricalSpecialData
 *
 * @property int $id
 * @property int $item_id
 * @property int $active_copies
 * @property int $unique_owners
 * @property int $views_today
 * @property int $avg_daily_views
 * @property int|null $avg_price
 * @property int $volume_hoarded
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Item\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData query()
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereActiveCopies($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereAvgDailyViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereAvgPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereUniqueOwners($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereViewsToday($value)
 * @method static \Illuminate\Database\Eloquent\Builder|HistoricalSpecialData whereVolumeHoarded($value)
 * @mixin \Eloquent
 */
class HistoricalSpecialData extends Model
{
    protected $fillable = [
        'item_id',
        'active_copies',
        'unique_owners',
        'views_today',
        'avg_daily_views',
        'avg_price',
        'volume_hoarded'
    ];

    /**
     * Owning item
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo 
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
