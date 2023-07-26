<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

use App\Models\Item\SpecialSeller;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\User\Crate
 *
 * @property \App\Models\Item\Item|\App\Models\Set\SetPass $crateable
 * @property int $id
 * @property int $user_id
 * @property int $crateable_id
 * @property int $crateable_type
 * @property int|null $item_id
 * @property int $serial
 * @property int $own
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read SpecialSeller|null $specialSeller
 * @property-read \App\Models\User\User $user
 * @method static \Database\Factories\User\CrateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Crate itemId($item)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Crate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Crate owned()
 * @method static \Illuminate\Database\Eloquent\Builder|Crate ownedBy($owner)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereCrateableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereCrateableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereOwn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereSerial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Crate whereUserId($value)
 * @mixin \Eloquent
 */
class Crate extends Model
{
    use HasFactory;

    protected $table = 'crate';

    protected $dateFormat = 'Y-m-d H:i:s.u';

    public $fillable = [
        'user_id', 'crateable_id', 'crateable_type', 'serial', 'own'
    ];

    protected static function booted()
    {
        static::creating(function (Crate $crate) {
            $crate->setSerial();
        });
    }

    private function setSerial()
    {
        if (isset($this->serial))
            return;
        if (!isset($this->crateable_id)) {
            Log::error('Crate being created with null crateable_id');
            return;
        }

        $this->serial = $this::where([['crateable_id', $this->crateable_id], ['crateable_type', $this->crateable_type]])
            ->max('serial') + 1;
    }

    public function scopeItemId($query, $item)
    {
        return $query->where([['crateable_id', $item], ['crateable_type', 1]]);
    }

    public function scopeOwnedBy($query, $owner)
    {
        return $query->where([['user_id', $owner], ['own', 1]]);
    }

    public function scopeOwned($query)
    {
        return $query->where('own', 1);
    }

    public function crateable(): MorphTo
    {
        return $this->morphTo();
    }

    public function specialSeller(): HasOne
    {
        return $this->hasOne(SpecialSeller::class)->whereRaw('`crate`.`user_id` = `special_sellers`.`user_id`')->active();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->select(['id', 'username']);
    }
}
