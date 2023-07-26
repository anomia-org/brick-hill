<?php

namespace App\Models\Item;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User\User;
use App\Models\User\Crate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Item\SpecialSeller
 *
 * @property int $id
 * @property int $user_id
 * @property int $crate_id
 * @property int $item_id
 * @property int $bucks
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Crate $crate
 * @property-read User $creator
 * @property-read mixed $serial
 * @property-read \App\Models\Item\Item $item
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller active()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller crateId($crate)
 * @method static \Database\Factories\Item\SpecialSellerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller itemId($item)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller query()
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller userId($user)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereBucks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereCrateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SpecialSeller whereUserId($value)
 * @mixin \Eloquent
 */
class SpecialSeller extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id', 'item_id', 'crate_id', 'bucks', 'active'
    ];

    protected $hidden = [
        'crate'
    ];

    protected static function booted()
    {
        static::updated(function (SpecialSeller $seller) {
            $seller->item->searchable();
        });

        static::created(function (SpecialSeller $seller) {
            $seller->item->searchable();
        });
    }

    public function scopeCrateId($query, $crate)
    {
        return $query->where('crate_id', $crate);
    }

    public function scopeUserId($query, $user)
    {
        return $query->where('user_id', $user);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeItemId($query, $item)
    {
        return $query->where('item_id', $item);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function crate(): BelongsTo
    {
        return $this->belongsTo(Crate::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function getSerialAttribute()
    {
        return $this->crate->serial;
    }
}
